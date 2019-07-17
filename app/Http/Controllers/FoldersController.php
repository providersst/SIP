<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Folder, TemporaryFile};
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
    public function index()
    {
        $folders = Folder::with('user')->get();

        $files = TemporaryFile::all();
        $files->map(function($file) {
          if($file->created_at < now()->addMinutes(1)) {
            if(Storage::exists($file->path)) {
                Storage::delete($file->path);
            }
            $file->delete();
          }
        });

        return view('folders.index', compact('folders'));
    }

    public function downloadAsZip($id, Request $request)
    {
        $folder = Folder::uuid($id);

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
        $folders = Folder::with('user')->get();
        return view('folders.create', compact('folders'));
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

        $data['user_id'] = $request->user()->id;

        $path = 'archives';
        $parentId = null;

        if($request->has('folder_id') && $request->get('folder_id') !== null) {
            $folderParent = Folder::find($data['folder_id']);
            $actualPath = $folderParent->path;
            $realPath = $actualPath != '/' ? $actualPath : '';
            $path = $realPath .'/'. $data['name'];
            $parentId = $folderParent->id;
        } elseif($request->get('folder_id') == null) {
            $path = $path.'/'. $data['name'];
        }

        $data['path'] = $path;
        $data['parent_id'] = $parentId;

        //dd($data);

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

        return redirect()->route('folders.show', $folder->uuid);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $folder = Folder::uuid($id);
        return view('folders.show', compact('folder'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
