<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;


class TwoFactorController extends Controller
{
    public function setup2FA()
    {
        $google2fa = new Google2FA();
        $user = Auth::user();

        if (!$user->google2fa_secret) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $QR_Image = $this->generateQRCode(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        return view('2fa.setup', ['QR_Image' => $QR_Image, 'secret' => $user->google2fa_secret]);
    }

    private function generateQRCode($appName, $email, $secret)
    {
        $google2fa = new Google2FA();

        $inlineUrl = $google2fa->getQRCodeUrl(
            $appName,
            $email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(256),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        return 'data:image/svg+xml;base64,' . base64_encode($writer->writeString($inlineUrl));
    }

    public function postSetup2FA(Request $request)
    {
        $request->validate([
            'secret' => 'required',
            'code' => 'required',
        ]);

        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($request->secret, $request->code);

        if ($valid) {

            $user = Auth::user();
            $user->google2fa_secret = $request->secret;
            $user->save();

            return redirect('/home')->with('success', '2FA is enabled successfully.');
        } else {
            return redirect()->back()->withErrors(['Invalid verification code, please try again.']);
        }
    }

    public function validate2FA()
    {
        return view('2fa.validate');
    }

    public function postValidate2FA(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $google2fa = new Google2FA();
        $user = Auth::user();

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->code);

        if ($valid) {
            $request->session()->put('2fa_authenticated', true);
            return redirect('/home');
        } else {
            return redirect()->back()->withErrors(['Invalid verification code, please try again.']);
        }
    }
}
