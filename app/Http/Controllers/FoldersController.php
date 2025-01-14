<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Folder, TemporaryFile};
use App\Models\Folder\Group\Permission as FolderGroupPermission;
use App\Models\Folder\User\Permission as FolderUserPermission;
use App\Models\Department;
use App\Helpers\Helper;
use App\User;
use Auth;
use File;
use Storage;
use Zipper;

class FoldersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if(!Auth::user()->hasPermission('view.pastas')) {
            return abort(403, 'Unauthorized action.');
        }

        $folders = $user->foldersPermissions->map(function($permission) {
            return $permission->folder;
        });

        if($user->isAdmin()) {
            $folders = Folder::all();
        }

        return view('folders.index', compact('folders'));
    }

    public function downloadAsZip($id, Request $request)
    {
        $folder = Folder::uuid($id);

        $permission = $folder->permissionsForUser->where('user_id', auth()->user()->id)->first();
        $donload = $permission->donload ?? false;

        if(!$donload && !$request->user()->isAdmin()) {
            notify()->flash('Erro em realizar Download', 'error', [
              'text' => 'Você não tem permissão para esta ação.'
            ]);
            return back();
        }

        $filename = $folder->name . '-arquivos-'.time().'.zip';

        $path = 'app/'.$folder->path;
        $fileRealPath = $path.'/'.$filename;

        $zipper = new \Chumper\Zipper\Zipper;
        $files = glob(storage_path($path));
        $zipper->make(storage_path('app/zipper/'.$filename))->add($files);
        $zipper->close();

        $filePath = 'zipper/'.$filename;

        if(Storage::exists($filePath)) {

            TemporaryFile::create([
              'user_id' => auth()->user()->id,
              'path' => $filePath,
            ]);

            return Storage::download($filePath);
        }

        notify()->flash('Erro Inesperado', 'success', [
          'text' => 'Não foi possivel encontrar o arquivo para download.'
        ]);

        return back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->hasPermission('create.pastas')) {
            return abort(403, 'Unauthorized action.');
        }

        $user = auth()->user();

        $folders = Folder::with('user')->get();

        $departments = Helper::departments();

        if(!$user->isAdmin()) {
            $departments = $departments->where('id', $user->person->department_id);
        }

        return view('folders.create', compact('folders', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->request->all();

        $user = $request->user();

        $data['user_id'] = $user->id;

        $path = 'archives';
        $parentId = null;

        if($request->has('folder_id') && $request->get('folder_id') !== null) {

            $folderParent = Folder::find($data['folder_id']);

            if($folderParent->parent) {
              if($folderParent->parent->parent) {
                if($folderParent->parent->parent->parent) {
                  if($folderParent->parent->parent->parent->parent) {
                    if($folderParent->parent->parent->parent->parent->parent) {
                      notify()->flash('Operação não permitida', 'error', [
                        'text' => 'Não é possível criar mais de 5 subpastas por diretório.'
                      ]);
                      return back();
                    }
                  }
                }
              }
            }

            $actualPath = $folderParent->path;
            $realPath = $actualPath != '/' ? $actualPath : '';
            $path = $realPath .'/'. $data['name'];
            $parentId = $folderParent->id;
        } elseif($request->get('folder_id') == null) {
            $path = $path.'/'. $data['name'];
        }

        $data['path'] = $path;
        $data['parent_id'] = $parentId;

        if(!File::isDirectory($path)) {
            $isDir = Storage::makeDirectory($path);
            if(!$isDir) {

              notify()->flash('Operação não permitida', 'error', [
                'text' => 'Esta pasta não pode ser criada.'
              ]);

              return back();

            }
        }

        $hasFolder = Folder::where('path', $path)->where('name', $data['name'])->get();

        if($hasFolder->isNotEmpty()) {

          notify()->flash('Operação não permitida', 'error', [
            'text' => 'Esta pasta já existe.'
          ]);

          return back();
        }

        $folder = Folder::create($data);

        if($request->has('departments')) {

          foreach ($data['departments'] as $key => $depto) {

              $department = Department::find($depto);

              if(!$department) {
                  //
              }

              FolderGroupPermission::create([
                'group_id' => $department->id,
                'folder_id' => $folder->id,
                'read' => $request->has('read'),
                'edit' => $request->has('edit'),
                'share' => $request->has('share'),
                'download' => $request->has('download'),
                'delete' => $request->has('delete'),
              ]);

              foreach ($department->people as $key => $person) {

                FolderUserPermission::create([
                  'user_id' => $person->user->id,
                  'folder_id' => $folder->id,
                  'read' => $request->has('read'),
                  'edit' => $request->has('edit'),
                  'share' => $request->has('share'),
                  'download' => $request->has('download'),
                  'delete' => $request->has('delete'),
                ]);

              }

          }

        } else {

            FolderUserPermission::create([
              'user_id' => $user->id,
              'folder_id' => $folder->id,
              'read' => $request->has('read'),
              'edit' => $request->has('edit'),
              'share' => $request->has('share'),
              'download' => $request->has('download'),
              'delete' => $request->has('delete'),
            ]);

        }

        return redirect()->route('folders.show', $folder->uuid);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        if(!Auth::user()->hasPermission('view.pastas')) {
            return abort(403, 'Unauthorized action.');
        }

        $user = $request->user();

        $folder = Folder::uuid($id);

        $permission = $folder->permissionsForUser->where('user_id', auth()->user()->id)->first();
        $read = $permission->read ?? false;

        if(!$read && !$request->user()->isAdmin()) {
            notify()->flash('Erro em acessar a Pasta', 'error', [
              'text' => 'Você não tem acesso à pasta requerida.'
            ]);
            return back();
        }

        $slug = 'list-style-folders-index';
        $listStyle = 'list';

        if(Helper::has($slug)) {
            $listStyle = Helper::get($slug);
        }

        if($request->has('list')) {
            $listStyle = Helper::create($slug, $request->get('list'));
        };

        return view('folders.show', compact('folder', 'listStyle', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $folder = Folder::uuid($id);

        $permission = $folder->permissionsForUser
        ->where('user_id', auth()->user()->id)
        ->first();

        $edit = $permission->edit ?? false;

        if(!$edit && !Auth::user()->isAdmin()) {
            return abort(403, 'Unauthorized action.');
        }

        $folders = Folder::with('user')->get();
        return view('folders.edit', compact('folders', 'folder'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $folder = Folder::uuid($id);

        $data = $request->request->all();

        $user = $request->user();

        $data['user_id'] = $user->id;
        $data['name'] = $folder->name;

        $path = 'archives';
        $parentId = null;

        if($request->has('folder_id') && $request->get('folder_id') !== null) {
            $folderParent = Folder::find($data['folder_id']);

            if($folderParent->parent) {

              if($folder->id == $folderParent->parent->id) {
                notify()->flash('Operação não permitida', 'error', [
                  'text' => 'Não é possível esta pasta para seu subdiretório.'
                ]);
                return back();
              }

              if($folderParent->parent->parent) {

                if($folder->id == $folderParent->parent->parent->id) {
                  notify()->flash('Operação não permitida', 'error', [
                    'text' => 'Não é possível esta pasta para seu subdiretório.'
                  ]);
                  return back();
                }

                if($folderParent->parent->parent->parent) {

                  if($folder->id == $folderParent->parent->parent->parent->id) {
                    notify()->flash('Operação não permitida', 'error', [
                      'text' => 'Não é possível esta pasta para seu subdiretório.'
                    ]);
                    return back();
                  }

                  if($folderParent->parent->parent->parent->parent) {

                    if($folder->id == $folderParent->parent->parent->parent->parent->id) {
                      notify()->flash('Operação não permitida', 'error', [
                        'text' => 'Não é possível esta pasta para seu subdiretório.'
                      ]);
                      return back();
                    }

                    if($folderParent->parent->parent->parent->parent->parent) {
                      notify()->flash('Operação não permitida', 'error', [
                        'text' => 'Não é possível criar mais de 5 subpastas por diretório.'
                      ]);
                      return back();
                    }
                  }
                }
              }
            }

            $actualPath = $folderParent->path;
            $realPath = $actualPath != '/' ? $actualPath : '';
            $path = $realPath .'/'. $data['name'];
            $parentId = $folderParent->id;
        } elseif($request->get('folder_id') == null) {
            $path = $path.'/'. $data['name'];
        }

        $data['path'] = $path;
        $data['parent_id'] = $parentId;

        if(!File::isDirectory($path)) {

            if($folder->path != $path) {
              $hasMoved = Storage::move($folder->path, $path);
              if(!$hasMoved) {

                notify()->flash('Operação não permitida', 'error', [
                  'text' => 'Não é possivel mover a pasta para o destino informado.'
                ]);

                return back();

              }
            }

        }

        notify()->flash('Sucesso', 'error', [
          'text' => 'Pasta editada com sucesso.'
        ]);

        $folder->update($data);

        return redirect()->route('folders.show', $folder->uuid);
    }

    public function verifyParentPath($value1, $value2)
    {
        if($value1 == $value2) {
          notify()->flash('Operação não permitida', 'error', [
            'text' => 'Não é possível criar mais de 5 subpastas por diretório.'
          ]);
          return back();
        }
    }

    public function changePermission($id, $user, $type = 'read', Request $request)
    {
        $folder = Folder::uuid($id);
        $user = User::uuid($user);

        $permission = $folder->permissionsForUser->where('user_id', $user->id)->first();

        if(!$permission) {
            $permission = FolderUserPermission::create([
              'user_id' => $user->id,
              'folder_id' => $folder->id,
              'read' => $type == 'read' ? 1 : 0,
              'edit' => $type == 'edit' ? 1 : 0,
              'share' => $type == 'share' ? 1 : 0,
              'download' => $type == 'download' ? 1 : 0,
              'delete' => $type == 'delete' ? 1 : 0,
            ]);
        } else {
            $permission->update([$type => !$permission->{$type}]);
        }

        return response()->json([
          'success' => true,
          'message' => 'Acesso modificado com sucesso',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Auth::user()->hasPermission('delete.pastas')) {
            return abort(403, 'Unauthorized action.');
        }

        try {

          $folder = Folder::uuid($id);

          $folder->archives->map(function($archive) {

            $archive->permissionsForUser->map(function($archive) {
              $archive->delete();
            });

            $archive->permissionsForGroup->map(function($archive) {
              $archive->delete();
            });

            if(Storage::exists($archive->path)) {
                Storage::delete($archive->path);
            }

            $archive->delete();

          });

          $folder->permissionsForUser->map(function($folder) {
            $folder->delete();
          });

          $folder->permissionsForGroup->map(function($folder) {
            $folder->delete();
          });

          if(Storage::exists($folder->path)) {
              Storage::deleteDirectory($folder->path);
          }

          $route = $folder->parent ? route('folders.show', $folder->parent->uuid) : route('folders.index');

          $folder->delete();

          return response()->json([
            'success' => true,
            'message' => 'Pasta removida com sucesso.',
            'route' => $route
          ]);

        } catch(\Exception $e) {
          return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'route' => route('folders.index')
          ]);
        }
    }
}
