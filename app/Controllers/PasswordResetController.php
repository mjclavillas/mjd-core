<?php
namespace App\Controllers;

use Mark\MjdCore\Http\Controller;
use Mark\MjdCore\Http\Session;
use Mark\MjdCore\Http\Request;
use \Mark\MjdCore\Http\View;

use App\Services\MailService;
use App\Models\User;
class PasswordResetController extends Controller
{
    public function showRequestForm() {
        return $this->view('forgot-password');
    }

    public function sendResetLink(Request $request, MailService $mailer)
    {
        $email = $request->input('email');
        
        $user = User::where('email', $email)->first();

        $token = bin2hex(random_bytes(32));
        $link = $_ENV['APP_URL'] . "reset-password/" . $token;

        $body = View::render('emails/reset', [
            'token' => $token,
            'link'  => $link
        ]);

        $sent = $mailer->send($email, 'AUTH_RECOVERY_PROTOCOL', $body);

        if (!$sent) {
            Session::flash('error', '[ERR]: Mail delivery failure.');
        }
        Session::flash('success', '[SYSTEM]: If that account exists, a link has been dispatched.');

        return $this->redirect('/login');
    }

    public function showResetForm($token) {
        return $this->view('reset-password', ['token' => $token]);
    }

    public function updatePassword()
    {
        $token = $_POST['token'];
        $newPassword = $_POST['password'];

        $reset = $this->db->query(
            "SELECT * FROM password_resets WHERE token = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)", 
            [$token]
        )->first();

        if (!$reset) {
            Session::flash('error', '[ERR]: Security token invalid or expired.');
            return $this->redirect('/forgot-password');
        }

        $this->db->query("UPDATE users SET password = ? WHERE email = ?", [
            password_hash($newPassword, PASSWORD_BCRYPT),
            $reset['email']
        ]);

        $this->db->query("DELETE FROM password_resets WHERE email = ?", [$reset['email']]);

        Session::flash('success', '[SYSTEM]: Access credentials updated. You may now authenticate.');
        return $this->redirect('/login');
    }
}