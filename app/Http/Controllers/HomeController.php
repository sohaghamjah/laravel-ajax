<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Traits\Uploadable;

class HomeController extends Controller
{
    use Uploadable;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $roles = Role::all();
        $districts = Location::where('parent_id',0)->orderBy('location_name','asc')->get();
        return view('home', compact('roles','districts'));
    }

    public function userList(Request $request){
        if($request -> ajax()){
            $user = new User();


            // Filter datatable

            if(!empty($request->name)){
                $user->setName($request->name);
            }
            if(!empty($request->email)){
                $user->setEmail($request->email);
            }
            if(!empty($request->mobile_no)){
                $user->setMobileNo($request->mobile_no);
            }
            if(!empty($request->district_id)){
                $user->setDistrictId($request->district_id);
            }
            if(!empty($request->upazila_id)){
                $user->setUpazilaId($request->upazila_id);
            }
            if(!empty($request->role_id)){
                $user->setRoleId($request->role_id);
            }
            if(!empty($request->status)){
                $user->setStatus($request->status);
            }


            // Show uer list
            $user -> setOrderValue($request->input('order.0.column'));
            $user -> setDirValue($request->input('order.0.dir'));
            $user -> setLengthValue($request->input('length'));
            $user -> setStartValue($request->input('start'));

            $list = $user -> getList();

            $data = [];
            $no = $request->input('start');
            foreach ($list as $value) {
                $no++;
                $action = '';
                $action .= ' <a style="cursor: pointer" class="dropdown-item edit_data" data-id="'.$value->id.'"><i class="fas fa-edit text-primary"></i> Edit</a>';
                $action .= ' <a style="cursor: pointer" class="dropdown-item view_data" data-id="'.$value->id.'"><i class="fas fa-eye text-warning"></i> View</a>';
                $action .= ' <a style="cursor: pointer" class="dropdown-item delete_data" data-name="'.$value->name.'" data-id="'.$value->id.'"><i class="fas fa-trash text-danger"></i> Delete</a>';

                $btngroup = '<div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-th-list"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        '.$action.'
                    </div>
                </div>';

                $row = [];
                $row []    = ' <div class="form-check">
                                <input value="'.$value->id.'" name="did[]" class="form-check-input select_data" onchange="selectSingleItem('.$value->id.')" type="checkbox" value="" id="checkBox'.$value->id.'">
                                <label class="form-check-label" for="checkBox'.$value->id.'">
                                </label>
                            </div>';
                $row []    = $no;
                $row []    = $this->avatar($value->name, $value->avatar);
                $row []    = $value->name;
                $row []    = $value->role->role_name;
                $row []    = $value->email;
                $row []    = $value->mobile_no;
                $row []    = $value->district->location_name;
                $row []    = $value->upazila->location_name;
                $row []    = $value->postal_code;
                $row []    = $value->email_verified_at ? '<span class="badge badge-pill badge-success p-1">Verified</span>' : '<span class="badge badge-pill badge-danger p-1">Unverified</span>';
                $row []    = $this->toggleButton($value->status,$value->id);
                $row []    = $btngroup;
                $data[]    = $row;
            }
            $output = array(
                "draw" => $request->input('draw'),
                "recordsTotal"    => $user -> countFilter(),
                "recordsFiltered" => $user -> countAll(),
                "data"            => $data,
            );

            echo json_encode($output);
        }
    }

    private function toggleButton($status,$id){
        $checked = $status == 1 ? 'checked' : '';
        return '<label class="switch">
        <input type="checkbox" '.$checked.' class="changeStatus" data-id="'.$id.'">
        <span class="slider round"></span>
        </label>';
    }

    private function avatar($name, $avatar = null){
        return !empty($avatar) ? '<img style="width: 60px" src="'.asset("storage/".USER_AVATAR.$avatar).'" alt="'.$name.'" />' : '<img style="width: 60px" src="'.asset("svg/user.svg").'" alt="'.$name.'" />';
    }

    /**
     * store function
     *
     * @param UserFormRequest $request
     * @return void
     */
    public function store(UserFormRequest $request){
        $data = $request->validated();
        $collection = collect($data)->except('avatar','password_confirmation');
        if($request -> file('avatar')){
           $avatar = $this->uploadFile($request -> file('avatar'), USER_AVATAR);
           $collection = $collection -> merge(compact('avatar'));
           if(!empty($request -> old_avatar)){
               $this->deleteFile($request->old_avatar, USER_AVATAR);
           }
        }
        $result = User::updateOrCreate(['id'=>$request->update_id], $collection->all());
        if($result){
            $output = ['status' => 'success', 'message' => 'Data has been saved succesfull'];
        }else{
            if(!empty($avatar)){
                $this->deleteFile($avatar, USER_AVATAR);
            }
            $output = ['status' => 'error', 'message' => 'Data can not saved'];
        }
        return response()->json($output);
    }

    /**
     * show function
     *
     * @param Request $request
     * @return void
     */
    public function show(Request $request){
        if($request -> ajax()){
            $data = User::with(['role:id,role_name', 'district:id,location_name', 'upazila:id,location_name']) -> find($request -> id);
            if($data){
                $output['view_data'] = view('user-details', compact('data'))->render();
                $output['name'] = $data->name;
            }else{
                $output['view_data'] = '';
                $output['name'] = '';
            }
            return response() -> json($output);
        }
    }

    /**
     * edit function
     *
     * @param Request $request
     * @return void
     */
    public function edit(Request $request){
        if($request -> ajax()){
            $data = User::toBase()->find($request -> id);
            if($data){
                $output = $data;
            }else{
                $output = '';
            }
            return response() -> json($output);
        }
    }

    public function changeStatus(Request $request){
        if($request -> ajax()){
            if($request->id && $request->status){
                    $result = User::find($request->id)->update([
                        'status' => $request->status,
                    ]);
                    if($result){
                        $output = ['status' => 'success', 'message' => 'Status has been updated succesfull'];
                    }else{
                        $output = ['status' => 'error', 'message' => 'Status can not update'];
                    }
                }else{
                    $output = ['status' => 'error', 'message' => 'Status can not update'];
                }
            return response() -> json($output);
        }
    }

    public function bulkActionDelete(Request $request){
        if($request -> ajax()){
            $avatars = User::toBase()->select('avatar')->whereIn('id',$request -> id);
            $result = User::destroy($request->id);
            if($result){
                if(!empty($avatars)){
                    foreach($avatars as $avatar){
                        if(!empty($avatar -> avatar)){
                            $this->deleteFile($avatar -> avatar, USER_AVATAR);
                        }
                    }
                }
                $output = ['status' => 'success', 'message' => 'Data has been deleted succesfull'];
            }else{
                $output = ['status' => 'error', 'message' => 'Data can not delete'];
            }
            return response() -> json($output);
        }
    }

    /**
     * delete function
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request){
        if($request -> ajax()){
            $data = User::find($request -> id);
            if($data){
                $avatar = $data->avatar;
                if($data->delete()){
                    if(!empty($avatar)){
                        $this->deleteFile($avatar, USER_AVATAR);
                    }
                    $output = ['status' => 'success', 'message' => 'Data has been deleted succesfull'];
                }else{
                    $output = ['status' => 'error', 'message' => 'Data can not delete'];
                }
            }else{
                $output = ['status' => 'error', 'message' => 'Data can not delete'];
            }
            return response() -> json($output);
        }
    }

    /**
     * upazilaList function
     *
     * @param Request $request
     * @return void
     */
    public function upazilaList(Request $request){
        if ($request->ajax()) {
            if ($request->district_id) {
                $output = '<option value="">Select Please</option>';
                $upazilas = Location::where('parent_id', $request->district_id)->orderBy('location_name', 'asc')->get();
                if (!$upazilas->isEmpty()) {
                    foreach ($upazilas as $value) {
                        $output .= '<option value="' . $value->id . '">' . $value->location_name . '</option>';
                    }
                }
                return response()->json($output);
            }
        }
    }
}
