<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ForgetPasswordRequest;

class ForgotPasswordController extends Controller
{

         use ApiResponses;
    
    /**
         * Send OTP Endpoint
         *
         * This API endpoint facilitates the sending of a One-Time Password (OTP) to a designated recipient.
         * The endpoint is designed to generate a unique OTP and deliver it via the chosen communication channel,
         * ensuring secure transmission for verification and authentication purposes.
         * The implementation of this endpoint typically involves specifying the recipient's information
         * and triggering the delivery of the OTP through the configured communication method, such as SMS, email, or other means.
         *
         * @param ForgetPasswordRequest $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function sendOtp(ForgetPasswordRequest $request)
        {
            $validatedEmail = $request->validated();
            try {
                $user = User::where('email', $validatedEmail['email'])->first();
    
                if (!$user) {
                    return response()->json(['message' => 'User not found'], 404);
                }
    
                $otp = generateOtp();
    
                $user->password_resets()->create([
                    'otp' => $otp,
                    'expires_at' => Carbon::now()->addMinutes(60),
                    'created_at' => Carbon::now(),
                ]);
                // Mail::to($user->email)->send(new OTPMail($otp,$user->name));
    
                return response()->json(['message' => 'One-Time Password (OTP) has been successfully sent to Your Email']);
            } catch (Exception $e) {
                return $this->errorResponse($e->getMessage(),'Something went wrong',Response::HTTP_BAD_REQUEST);
            }
        }
    
    
        /**
         * Validate OTP Endpoint
         *
         * This API endpoint validates the One-Time Password (OTP) provided for the password reset process.
         * It checks the OTP's validity and expiration time before allowing the password to be reset.
         *
         * @param ResetPasswordRequest $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function validateOtp(ResetPasswordRequest $request)
        {
            $validatedData = $request->validated();
            try {
                $user = User::where('email', $validatedData['email'])->first();
    
                if (!$user) {
                    return response()->json(['message' => 'User not found'], 404);
                }
    
                $passwordReset = $user->password_resets()->where('otp', $validatedData['otp'])->first();
    
                if (!$passwordReset || $passwordReset->expires_at < now()) {
                    return response()->json(['message' => 'Invalid OTP'], 401);
                }
    
                $user->password = $validatedData['password'];
                $user->save();
    
                $passwordReset->delete();
    
                return response()->json(['message' => 'Password reset successfully']);
    
            } catch (Exception $e) {
                return $this->errorResponse($e->getMessage(), 'Something went wrong', Response::HTTP_BAD_REQUEST);
            }
        }
}
