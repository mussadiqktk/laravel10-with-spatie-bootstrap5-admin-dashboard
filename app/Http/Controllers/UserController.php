<?php

namespace App\Http\Controllers;

use App\DTO\UserDto;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    private $_repo = null;
    private $_directory = 'auth/pages/users';
    private $_route = 'users';

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(UserRepository $repo)
    {
        $this->_repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['all'] = $this->_repo->get_all_users();
        // dd($data['all']);
        return view($this->_directory . '.all', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
       
        return view($this->_directory . '.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', 
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
    
        return redirect()->route('users.index')->with('success', 'User created successfully.');

        // try {
        //     $this->_repo->store(UserDto::fromRequest($request->validated()));
        //     return redirect()->route($this->_route . '.index')->with('success', 'Successfully created.');
        // } catch (\Throwable $th) {
        //     return redirect()->route($this->_route . '.index')->with('error', 'Something went wrong.');
        // }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->_repo->show($id);

        if ($data == null) {
            abort(404);
        }

        return view($this->_directory . '.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->_repo->show($id);

        if ($data == null) {
            abort(404);
        }

        return view($this->_directory . '.edit', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function editprofile()
    {
        $data = Auth::user();
        return view($this->_directory . '.my_profile', compact('data'));
    }

    /**
     * Update My profile.
     *
     * @param Request Validation $validation
     * @return \Illuminate\Http\Response
     */

    public function updatemyprofile(Request $request)
    {
        $user = Auth::user();

        $requestData = $request->except(['_token']);

        $user->update($requestData);

        return redirect()->route('myprofile');
    }


    // public function updatemyprofile(UserRequest $request)
    // {
    //     $this->_repo->update(Auth::id(), UserDto::fromRequest($request->validated()));
    //     return redirect()->route('myprofile');
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param Request Validation $validation
     * @return \Illuminate\Http\Response
     */


     public function update(Request $request, $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);
        
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8', // Optional: Allow password update
        ]);

        // Update the user's information
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        if (isset($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }
        $user->save();

        // Redirect back to a specific route or respond with a success message
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
    // public function update(UserRequest $request, $id)
    // {
    //     try {
    //         $this->_repo->update($id, UserDto::fromRequest($request->validated()));
    //         return redirect()->route($this->_route . '.index')->with('success', 'Updated succesfully');
    //     } catch (\Throwable $th) {
    //         if ($th instanceof NotFoundHttpException) {
    //             $message = $th->getMessage(); // Get the exception message
    //             return redirect()->route($this->_route . '.index')->with('error', $message);
    //         } else {
    //             return redirect()->route($this->_route . '.index')->with('error', 'Something went wrong.');
    //         }
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->_repo->destroy($id);
            return redirect()->route($this->_route . '.index')->with('success', 'Deleted succesfully');
        } catch (\Throwable $th) {
            if ($th instanceof NotFoundHttpException) {
                $message = $th->getMessage(); // Get the exception message
                return redirect()->route($this->_route . '.index')->with('error', $message);
            } else {
                return redirect()->route($this->_route . '.index')->with('error', 'Something went wrong.');
            }
        }
    }
}
