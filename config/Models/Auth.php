<?php
use App\Models\User;
use Database\DB;

class Auth
{

    public static function check($type = "login", $value = null)
    {
        switch ($type):

            case "login":

                if (isset($_SESSION['token'])):

                    if (isset(Token::check($_SESSION['token'])->data->users->username) || isset(Token::check($_SESSION['token'])->data->users->token)):

                        $token = Token::check($_SESSION['token'])->data->users;

                        if (empty(User::where(['username' => $token->username, 'token' => $_SESSION['token']])->count())):
                            return false;
                        else:
                            return true;
                        endif;

                    else:
                        return false;
                    endif;

                else:
                    return false;
                endif;


            default:

                if (empty(User::where([$type => $value])->count())):
                    return false;
                else:
                    return true;
                endif;

        endswitch;

    }

    public function logout()
    {
        unset($_SESSION['token']);
        return true;
    }

    public static function user()
    {

        if (isset($_SESSION['token'])):

            if (isset(Token::check($_SESSION['token'])->data->users->username)):

                $token = Token::check($_SESSION['token'])->data->users;

                if (empty(User::where(['username' => $token->username, 'token' => $_SESSION['token']])->count())):
                    return false;
                else:
                    return User::where(['username' => $token->username, 'token' => $_SESSION['token']])->first();
                endif;

            else:
                return false;
            endif;

        else:
            return false;
        endif;

    }

    public static function login($data)
    {
        if (static::check()):
            return false;
        else:

            $login = [
                'users' => [
                    'username' => $data->username,
                    'email' => $data->email,
                    'DateLog' => time(),
                ],
            ];

            $token = Token::create($login);
            $_SESSION['token'] = $token;
            User::where(['username' => $data->username])->set(['token' => $token])->update();

            return true;
            
        endif;
    }

    public static function save($data)
    {
        if (static::user()):
            return false;
        elseif (empty(User::where(['username' => $data->username])->count())):

            if ($save = User::set($data)->insert()):
                $login = [
                    'users' => [
                        'username' => $data->username,
                        'email' => $data->email,
                        'DateLog' => time(),
                    ],
                ];

                $token = Token::create($login);
                $_SESSION['token'] = $token;

                User::where(['username' => $data->username])->set(['token' => $token])->update();

                return $save;

            else:
                return false;
            endif;

        else:
            return false;
        endif;
    }
}