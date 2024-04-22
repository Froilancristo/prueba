<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EstudiantesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Estudiante::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->input();
        $fullName= Str::of($inputs['nombre'])->replace(' ', '_')."_".Str::of($inputs['apellido'])->replace(' ', '_');
        $file = $request->file('foto');
        $path = $file->storeAs('img',$fullName.'.'.$file->getClientOriginalExtension(),'public');
        $url = Storage::url($path);
        $inputs['foto'] = $url;
        $e = Estudiante::create($inputs);
        return response()->json([
            'data'=>$e,
            'mensaje'=>"Estudiante dado de alta"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $e = Estudiante::find($id);
        if (isset($e)) {
             return response()->json([
                'data'=>$e,
                'mensaje'=>"Estudiante encontrado con exito",
            ]);
        } else {
             return response()->json([
                'error'=>true,
                'mensaje'=>"Estudiante no existe",
            ]);
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $e = Estudiante::find($id);
        
    
        if (isset($e)) {
            $inputs =  $request->input();
            $e->nombre = $inputs['nombre'];
            $e->apellido = $inputs['apellido'];
            $e->domicilio1 = $inputs['domicilio1'];
            $e->domicilio2 = $inputs['domicilio2'] ? $inputs['domicilio2'] : $e->domicilio2;
            $e->domicilio3 = $inputs['domicilio3'] ? $inputs['domicilio3'] : $e->domicilio3;

    
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $fullName = Str::of($request->nombre)->replace(' ', '_') . "_" . Str::of($request->apellido)->replace(' ', '_');
                
                // Guardar la imagen en la carpeta 'img' del almacenamiento público
                $path = $file->storeAs('img', $fullName . '.' . $file->getClientOriginalExtension(), 'public');
                
                // Obtener la URL de la imagen
                $url = Storage::url($path);
    
                // Eliminar la imagen anterior si existe
                $rutaImg = public_path($e->foto);
                if (file_exists($rutaImg)) {
                    unlink($rutaImg);
                }
    
                // Actualizar la URL de la imagen del estudiante
                $e->foto = $url;
            }
            
            if ($e->save()) {
                return response()->json([
                    'data' => $e,
                    'mensaje' => "Estudiante actualizado"
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'mensaje' => "Error al actualizar el estudiante"
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => "No existe el estudiante"
            ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $e = Estudiante::find($id);
        if (isset($e)) {

            $rutaImg = public_path($e->foto);
            $res = Estudiante::destroy($id);

            if($res){
                if (file_exists($rutaImg)) {
                    unlink($rutaImg);
                }
                 return response()->json([
                'data'=>$e,
                'mensaje'=>"El estudiante fue eliminado con exito",
            ]);
            }else{
                return response()->json([
                    'data'=>$e,
                    'mensaje'=>"Estudiante no existe",
                ]);
            }
            
        } else {
             return response()->json([
                'error'=>true,
                'mensaje'=>"Estudiante no existe",
            ]);
        }
    }
}
