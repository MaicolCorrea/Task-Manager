<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function PHPSTORM_META\map;

class usersController extends Controller {
    public function createUser ( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'name' => [ 'required', 'string', 'max:75' ],
            'email' => [ 'required', 'email', 'max:100' ],
            'verify_email' => [ 'nullable', 'max:100', 'email' ],
            'password' => [ 'required', Password::min( 8 )->letters()
            ->mixedCase()
            ->numbers()
            ->symbols() ]
        ] );

        if ( $validator->fails() ) {
            return response()->json( [ 'error' => $validator->errors() ], 400 );
        }

        $emailExits = User::where( 'email', $request->email )->first();

        if ( $emailExits ) {
            return response()->json( [ 'error' => 'Lo sentimos, este usuario ya existe.' ], 409 );
        }

        $hashedPassword = Hash::make( $request->password );

        $user = User::create( [
            'name' => $request->name,
            'email' => $request->email,
            'verify_email' => $request->verify_email,
            'password' => $hashedPassword
        ] );

        if ( $user ) {
            return response()->json( [ 'message' => 'Se ha creado el usuario con éxito.' ], 200 );
        } else {
            return response()->json( [ 'error' => 'No se ha podido crear el usuario, por favor verifique la información.' ], 500 );
        }
    }

    public function updateUser ( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'id' => [ 'required', 'numeric' ],
            'name' => [ 'required', 'string', 'max:75' ],
            'email' => [ 'required', 'email', 'max:100' ],
            'verify_email' => [ 'nullable', 'max:100', 'email' ],
            'password' => [ 'required', Password::min( 8 )->letters()
            ->mixedCase()
            ->numbers()
            ->symbols() ]
        ] );

        if ( $validator->fails() ) {
            return response()->json( [ 'error' => $validator->errors() ], 400 );
        }

        $UserExits = User::find( $request->id );
        if ( !$UserExits ) {
            return response()->json( [ 'error' => 'No se ha encontrado el usuario.' ], 404 );
        }

        $emailExits = User::where( 'email', $request->email )->first();

        if ( $emailExits ) {
            return response()->json( [ 'error' => 'Lo sentimos, este usuario ya existe.' ], 409 );
        }

        $hashedPassword = Hash::make( $request->password );

        $UserExits->update( [
            'name' => $request->name,
            'email' => $request->email,
            'verify_email' => $request->verify_email,
            'password' => $hashedPassword
        ] );

        if ( $UserExits ) {
            return response()->json( [ 'message' => 'Se ha creado el usuario con éxito.' ], 200 );
        } else {
            return response()->json( [ 'error' => 'No se ha podido crear el usuario, por favor verifique la información.' ], 500 );
        }
    }

    public function consultAllDataUsers() {
        $User = User::all();

        if ( $User ) {
            return response()->json( [ 'data' => $User ], 200 );
        } else {
            return response()->json( [
                'error' => 'No se han encontrado datos de ningún usuario.',
                'data' => []
            ], 500 );
        }
    }

    public function consultUserById(Request $request) {
        $User = User::select('*')
        ->where('id', '=', $request->id);

        if ( $User ) {
            return response()->json( [ 'data' => $User ], 200 );
        } else {
            return response()->json( [
                'error' => 'No se han encontrado datos de ningún usuario.',
                'data' => []
            ], 500 );
        }
    }

    public function changePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
            'password' => [ 'required', Password::min( 8 )->letters()
            ->mixedCase()
            ->numbers()
            ->symbols() ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $User = User::find($request->id);
        if (!$User) {
            return response()->json(['error' => "No se ha encotnrado el usuario."], 404);
        }

        $hashedPassword = Hash::make($request->password);

        $User->update([
            'password' => $hashedPassword
        ]);

        if ($User) {
            return response()->json(['message' => "Se ha cambiado la contraseña del usuario."], 200);
        } else {
            return response()->json(['error' => "No se ha podido cambiar la contraseña del usuario."], 500);
        }
    }

    // Falto el dato de cambio de estado del usuario en la tabla, después 
    // se ajusta para el método de cambio de estado. xd
}
