<?php
namespace App\Auth\Digma;

use Illuminate\Contracts\Auth\UserProvider;

class DigmaUserProvider implements UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        // TODO: Implement retrieveById() method.
        
        
        $qry = UserPoa::where('admin_id','=',$identifier);
        
        if($qry->count() >0)
        {
            $user = $qry->select('admin_id', 'username', 'first_name', 'last_name', 'email', 'password')->first();
            
            $attributes = array(
                'id' => $user->admin_id,
                'username' => $user->username,
                'password' => $user->password,
                'name' => $user->first_name . ' ' . $user->last_name,
            );
            
            return $user;
        }
        return null;
    }
    
    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.
        $qry = UserPoa::where('admin_id','=',$identifier)->where('remember_token','=',$token);
        
        if($qry->count() >0)
        {
            $user = $qry->select('admin_id', 'username', 'first_name', 'last_name', 'email', 'password')->first();
            
            $attributes = array(
                'id' => $user->admin_id,
                'username' => $user->username,
                'password' => $user->password,
                'name' => $user->first_name . ' ' . $user->last_name,
            );
            
            return $user;
        }
        return null;
        
        
        
    }
    
    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        // TODO: Implement updateRememberToken() method.
        $user->setRememberToken($token);
        
        $user->save();
        
    }
    
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // TODO: Implement retrieveByCredentials() method.
        $qry = UserPoa::where('username','=',$credentials['username']);
        
        if($qry->count() >0)
        {
            $user = $qry->select('admin_id','username','first_name','last_name','email','password')->first();
            
            
            
            
            return $user;
        }
        return null;
        
        
    }
    
    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // TODO: Implement validateCredentials() method.
        // we'll assume if a user was retrieved, it's good
        
        if($user->username == $credentials['username'] && $user->getAuthPassword() == md5($credentials['password'].\Config::get('constants.SALT')))
        {
            
            $user->last_login_time = Carbon::now();
            $user->save();
            
            return true;
        }
        return false;
        
        
    }
}

