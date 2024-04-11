<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Client;
use App\Models\User;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Delete(
        path: "/users/{id}",
        operationId: "destroy-user",
        description: "Delete a user",
        security: [["bearerAuth" => ["role" => "admin"]],],
        tags: ["Users"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "User ID",
                in: "path", required: "true",
                schema: new OA\Schema(type: "integer", format: 'int64'))
        ],
        responses: [
            new OA\Response(response: 200,
                description: "Delete a user",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "status", type: "boolean"),
                    new OA\Property(property: "message", type: "string"),
                ], type: "object")
            )
        ]
    )]
    public function destroy(string $id) {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'status' => true,
            'message' => "User Destroyed successfully!",
        ]);
    }

    #[OA\Get(
        path: "/users/{id}",
        operationId: "profil",
        description: "Get user profile",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'token', type: 'string')
            ]),
        ),
        tags: ["Users"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "User ID",
                in: "path", required: "true",
                schema: new OA\Schema(type: "integer", format: 'int64'))
        ],
        responses: [
            new OA\Response(response: 200,
                description: "Get user profile",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "status", type: "boolean"),
                    new OA\Property(property: "message", properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'nom', type: 'string'),
                        new OA\Property(property: 'prenom', type: 'string'),
                        new OA\Property(property: 'adresse', type: 'string'),
                        new OA\Property(property: 'code_postal', type: 'string'),
                        new OA\Property(property: 'ville', type: 'string'),
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'email', type: 'string'),
                        new OA\Property(property: 'role', type: 'string'),
                        new OA\Property(property: 'password', type: 'string'),
                    ], type: "object")
                ], type: "object")
            )
        ]
    )]
    public function profil(string $id) {
        $user = User::findOrFail($id);
        $client = Client::all()->where('user_id',$id)->first();
        return response()->json([
            'status' => true,
            'message' =>  [
                'id'=>$client->id,
                'nom'=> $client->nom,
                'prenom' => $client->prenom,
                'adresse' => $client->adresse,
                'code_postal' => $client->code_postal,
                'ville' => $client->ville,
                'name' => $user->name,
                'email' => $user->email,
                'role' =>$user->role,
                'password' =>$user->password,
            ]
        ]);
    }

    #[OA\Put(
        path: "/users/{id}",
        operationId: "update-user",
        description: "Update a user",
        security: [["bearerAuth" => ["role" => "gestionnaire"]],],
        requestBody: new OA\RequestBody(
            description: "User data",
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: "name", type: "string"),
                new OA\Property(property: "email", type: "string"),
                new OA\Property(property: "password", type: "string"),
                new OA\Property(property: "role", type: "string"),
            ], type: "object")
        ),
        tags: ["Users"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "User ID",
                in: "path", required: "true",
                schema: new OA\Schema(type: "integer", format: 'int64'))
        ],
        responses: [
            new OA\Response(response: 200,
                description: "Update a user",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "status", type: "boolean"),
                    new OA\Property(property: "message", type: "string"),
                    new OA\Property(property: "user", ref: "#/components/schemas/User", type: "object"),
                ], type: "object")
            )
        ]
    )]
    public function update(UserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        if ($request->name) {
            $user->name = $request->name;
        }
        if ($request->email) {
            $user->email = $request->email;
        }
        if ($request->password) {
            $user->password = $request->password;
        }
        if ($request->role) {
            $user->role = $request->role;
        }

        $user->save();
        return response()->json([
            'status' => true,
            'message' => "User modifié avec succès",
            'user' => $user
        ]);
    }
}
