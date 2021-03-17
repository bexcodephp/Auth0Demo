<?php

namespace App\Http\Controllers\Auth;

use App;
use Auth;
use Redirect;
use App\Http\Controllers\Controller;

class Auth0IndexController extends Controller
{
    /**
     * Redirect to the Auth0 hosted login page
     *
     * @return mixed
     */
    public function login()
    {
        if (Auth::check()) {
            return redirect()->intended('/');
        }

        return App::make('auth0')->login(
            null,
            null,
            ['scope' => 'openid name email email_verified'],
            'code'
        );
    }

    /**
     * Log out of our app
     *
     * @return mixed
     */
    public function logout()
    {
        Auth::logout();

        $logoutUrl = sprintf(
            'https://%s/v2/logout?client_id=%s&returnTo=%s',
            config('laravel-auth0.domain'),
            config('laravel-auth0.client_id'),
            config('app.url')
        );

        return Redirect::intended($logoutUrl);
    }

    /**
     * Display the user's Auth0 data
     *
     * @return mixed
     */
    public function profile()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->profileinfo(Auth::user()->getUserInfo()['email']);
        // return view('profile')->with(
        //     'user',
        //     print_r(Auth::user()->getUserInfo(), true)
        // );
    }

    public function profileinfo($email)
    {
        $curl = curl_init();
            curl_setopt_array($curl, [
            CURLOPT_URL => "https://dev-toayeng8.us.auth0.com/api/v2/users?q=".$email."&search_engine=v3",
            //CURLOPT_URL => "https://dev-toayeng8.us.auth0.com/api/v2/users/auth0|602d1e2e6c534b0067e00b06",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Ims5Y2x6czJOQ3o5Ykt3dnRleVF5ayJ9.eyJpc3MiOiJodHRwczovL2Rldi10b2F5ZW5nOC51cy5hdXRoMC5jb20vIiwic3ViIjoiT1lLdFJuQllxZDk5UW5aTlI3bDdNZFlTd0h0d2UyTUxAY2xpZW50cyIsImF1ZCI6Imh0dHBzOi8vZGV2LXRvYXllbmc4LnVzLmF1dGgwLmNvbS9hcGkvdjIvIiwiaWF0IjoxNjE1OTg3NDcwLCJleHAiOjE2MTYwNzM4NzAsImF6cCI6Ik9ZS3RSbkJZcWQ5OVFuWk5SN2w3TWRZU3dIdHdlMk1MIiwic2NvcGUiOiJyZWFkOmNsaWVudF9ncmFudHMgY3JlYXRlOmNsaWVudF9ncmFudHMgZGVsZXRlOmNsaWVudF9ncmFudHMgdXBkYXRlOmNsaWVudF9ncmFudHMgcmVhZDp1c2VycyB1cGRhdGU6dXNlcnMgZGVsZXRlOnVzZXJzIGNyZWF0ZTp1c2VycyByZWFkOnVzZXJzX2FwcF9tZXRhZGF0YSB1cGRhdGU6dXNlcnNfYXBwX21ldGFkYXRhIGRlbGV0ZTp1c2Vyc19hcHBfbWV0YWRhdGEgY3JlYXRlOnVzZXJzX2FwcF9tZXRhZGF0YSByZWFkOnVzZXJfY3VzdG9tX2Jsb2NrcyBjcmVhdGU6dXNlcl9jdXN0b21fYmxvY2tzIGRlbGV0ZTp1c2VyX2N1c3RvbV9ibG9ja3MgY3JlYXRlOnVzZXJfdGlja2V0cyByZWFkOmNsaWVudHMgdXBkYXRlOmNsaWVudHMgZGVsZXRlOmNsaWVudHMgY3JlYXRlOmNsaWVudHMgcmVhZDpjbGllbnRfa2V5cyB1cGRhdGU6Y2xpZW50X2tleXMgZGVsZXRlOmNsaWVudF9rZXlzIGNyZWF0ZTpjbGllbnRfa2V5cyByZWFkOmNvbm5lY3Rpb25zIHVwZGF0ZTpjb25uZWN0aW9ucyBkZWxldGU6Y29ubmVjdGlvbnMgY3JlYXRlOmNvbm5lY3Rpb25zIHJlYWQ6cmVzb3VyY2Vfc2VydmVycyB1cGRhdGU6cmVzb3VyY2Vfc2VydmVycyBkZWxldGU6cmVzb3VyY2Vfc2VydmVycyBjcmVhdGU6cmVzb3VyY2Vfc2VydmVycyByZWFkOmRldmljZV9jcmVkZW50aWFscyB1cGRhdGU6ZGV2aWNlX2NyZWRlbnRpYWxzIGRlbGV0ZTpkZXZpY2VfY3JlZGVudGlhbHMgY3JlYXRlOmRldmljZV9jcmVkZW50aWFscyByZWFkOnJ1bGVzIHVwZGF0ZTpydWxlcyBkZWxldGU6cnVsZXMgY3JlYXRlOnJ1bGVzIHJlYWQ6cnVsZXNfY29uZmlncyB1cGRhdGU6cnVsZXNfY29uZmlncyBkZWxldGU6cnVsZXNfY29uZmlncyByZWFkOmhvb2tzIHVwZGF0ZTpob29rcyBkZWxldGU6aG9va3MgY3JlYXRlOmhvb2tzIHJlYWQ6YWN0aW9ucyB1cGRhdGU6YWN0aW9ucyBkZWxldGU6YWN0aW9ucyBjcmVhdGU6YWN0aW9ucyByZWFkOmVtYWlsX3Byb3ZpZGVyIHVwZGF0ZTplbWFpbF9wcm92aWRlciBkZWxldGU6ZW1haWxfcHJvdmlkZXIgY3JlYXRlOmVtYWlsX3Byb3ZpZGVyIGJsYWNrbGlzdDp0b2tlbnMgcmVhZDpzdGF0cyByZWFkOnRlbmFudF9zZXR0aW5ncyB1cGRhdGU6dGVuYW50X3NldHRpbmdzIHJlYWQ6bG9ncyByZWFkOmxvZ3NfdXNlcnMgcmVhZDpzaGllbGRzIGNyZWF0ZTpzaGllbGRzIHVwZGF0ZTpzaGllbGRzIGRlbGV0ZTpzaGllbGRzIHJlYWQ6YW5vbWFseV9ibG9ja3MgZGVsZXRlOmFub21hbHlfYmxvY2tzIHVwZGF0ZTp0cmlnZ2VycyByZWFkOnRyaWdnZXJzIHJlYWQ6Z3JhbnRzIGRlbGV0ZTpncmFudHMgcmVhZDpndWFyZGlhbl9mYWN0b3JzIHVwZGF0ZTpndWFyZGlhbl9mYWN0b3JzIHJlYWQ6Z3VhcmRpYW5fZW5yb2xsbWVudHMgZGVsZXRlOmd1YXJkaWFuX2Vucm9sbG1lbnRzIGNyZWF0ZTpndWFyZGlhbl9lbnJvbGxtZW50X3RpY2tldHMgcmVhZDp1c2VyX2lkcF90b2tlbnMgY3JlYXRlOnBhc3N3b3Jkc19jaGVja2luZ19qb2IgZGVsZXRlOnBhc3N3b3Jkc19jaGVja2luZ19qb2IgcmVhZDpjdXN0b21fZG9tYWlucyBkZWxldGU6Y3VzdG9tX2RvbWFpbnMgY3JlYXRlOmN1c3RvbV9kb21haW5zIHVwZGF0ZTpjdXN0b21fZG9tYWlucyByZWFkOmVtYWlsX3RlbXBsYXRlcyBjcmVhdGU6ZW1haWxfdGVtcGxhdGVzIHVwZGF0ZTplbWFpbF90ZW1wbGF0ZXMgcmVhZDptZmFfcG9saWNpZXMgdXBkYXRlOm1mYV9wb2xpY2llcyByZWFkOnJvbGVzIGNyZWF0ZTpyb2xlcyBkZWxldGU6cm9sZXMgdXBkYXRlOnJvbGVzIHJlYWQ6cHJvbXB0cyB1cGRhdGU6cHJvbXB0cyByZWFkOmJyYW5kaW5nIHVwZGF0ZTpicmFuZGluZyBkZWxldGU6YnJhbmRpbmcgcmVhZDpsb2dfc3RyZWFtcyBjcmVhdGU6bG9nX3N0cmVhbXMgZGVsZXRlOmxvZ19zdHJlYW1zIHVwZGF0ZTpsb2dfc3RyZWFtcyBjcmVhdGU6c2lnbmluZ19rZXlzIHJlYWQ6c2lnbmluZ19rZXlzIHVwZGF0ZTpzaWduaW5nX2tleXMgcmVhZDpsaW1pdHMgdXBkYXRlOmxpbWl0cyBjcmVhdGU6cm9sZV9tZW1iZXJzIHJlYWQ6cm9sZV9tZW1iZXJzIGRlbGV0ZTpyb2xlX21lbWJlcnMgcmVhZDplbnRpdGxlbWVudHMiLCJndHkiOiJjbGllbnQtY3JlZGVudGlhbHMifQ.J2bIm3Jb4gU2l_Oy4nKS2fIBUxXAJ-IQyKUOgb6Gkaxje8ztXXq8BwU77HkkO78Xo7YYs2byA3JDjUNLMgqUE8njWbnWTjBFLunolLQ2TGgh2HoysX3td_KWTQFXzxzFuzAIfHXVjrY1OTmIfqLuIqeGtedS4NxEvbR8U_1CIqgWy0JqY782MrMA-DdD5p5OrgagjLXSNgj1QvPSaZSEweWkenaacWk0tAwEF74e0ZB-XnR72jxXrxGuzxL9ztKGAiqzcfKiRW3pB2LIaGwuPRcSn13ZPoy3lCfN6xHhbSCRzse6K8QXYslnw1_kNF15lthdiRrSTySZjOog83EbSg"
            ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                dd($response);
            };
    }
}
