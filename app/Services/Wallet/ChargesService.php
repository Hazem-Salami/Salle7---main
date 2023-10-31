<?php

namespace App\Services\Wallet;

use App\Jobs\wallets\UserWalletJob;
use App\Models\User;
use App\Services\BaseService;
use App\Mail\VerificationMail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\verify\VerificationRequest;
use Carbon\Carbon;

class ChargesService extends BaseService
{

    public function createWallet(): Response
    {
        DB::beginTransaction();
        $user = User::find(auth()->user()->id);

        if ($user->wallet === null) {
            $wallet = $user->wallet()->create();

            try {
                UserWalletJob::dispatch(['email' => $user->email])->onQueue('admin');
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->customResponse(false, 'Bad Internet', null, 504);
            }
            DB::commit();
            return $this->customResponse(true, 'تم إنشاء محفظة بنكية لك بنجاح، شكراً', $wallet);
        }
        return $this->customResponse(false, 'لديك محفظة مسبقاً');
    }

    public function getStatus(): Response
    {
        $user = User::find(auth()->user()->id);

        if ($user->wallet === null) {
            return $this->customResponse(true, 'ليس لديك محفظة بنكية', 0);
        } else {
            return $this->customResponse(true, 'لديك محفظة بنكية', 1);
        }
    }

    public function getAmount(): Response
    {
        $user = User::find(auth()->user()->id);

        if ($user->wallet === null) {
            return $this->customResponse(false, 'ليس لديك محفظة بنكية');
        }
        return $this->customResponse(true, 'الرصيد الحالي', $user->wallet->amount);
    }

    public function charge(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return self::failed($validator->errors()->first());
        } else {
            $wallet = Wallet::where('user_id', auth()->user()->id)->first();
            if ($wallet === null) {
                return $this->failed('ليس لديك محفظة بنكية');
            }
            $amount = $wallet->amount;
            $wallet->amount += $request->get('amount');
            $wallet->save();

            WalletCharge::create([
                'user_id' => auth()->user()->id,
                'wallet_id' => $wallet->id,
                'difference' => $request->get('amount'),
                'new_amount' => $wallet->amount,
                'pre_mount' => $amount
            ]);
            return $this->success('تم الشحن بنجاح', $wallet->amount);
        }
    }

    public function getCharges()
    {
        $charges = WalletCharge::join('users', 'users.id', '=', 'wallet_charges.user_id')
            ->select(
                'wallet_charges.difference',
                'wallet_charges.pre_mount',
                'wallet_charges.new_amount',
                'users.first_name',
                'users.last_name'
            )
            ->paginate(10);
        return $this->success('charges log', $charges);
    }
}
