<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectInvitation;

class ProjectFiles extends Model
{
    protected $table = 'gc_project_files';
    protected $primaryKey= 'id';
    protected $fillable = [
        'id_project',
        'gc_company_id',
        'name',
        'type',
        'plan_type',
        'file_path',
        'file_extension',
        'gc_company_id'
    ];
    use HasFactory;

    public static function getSortedFilesGeneralContractor($data, $userData){

        $result = ProjectFiles::select(DB::raw("type as docType, name, file_path as fullPath,
                               id_project as projectId, id as docId,  file_extension, created_at,
                               COALESCE(plan_type,'All Other Drawings') as planTypes"))
                    ->where("id_project", $data['projectId'])
                    ->where(function ($sub_query) use ($userData, $data) {

                        $sub_query->orWhere('gc_company_id', '9999999')
                                 ->orWhere('gc_company_id', $userData['id_company']);

                        if ($data['docType']) {
                            $sub_query->where('type', $data['docType']);
                        }
                        if ($data['planSubTypes']) {
                            $sub_query->where('plan_type', $data['planSubTypes']);
                        }
                    })
                    ->groupBy(['name', 'type'])
                    ->orderBy($data['sortedBy'], $data['orderBy']);

        $all_data_count = $result->get()->toArray();
        $all_data = $result->offset($data['skip'])
                            ->limit($data['limit'])
                            ->get()->toArray();

        $data['all_data_count'] = $all_data_count;
        $data['all_data']= $all_data;

        return $data;
    }

    public static function getSortedFilesByCompanyId($data, $userData){

        $result = ProjectFiles::select(DB::raw("type as docType, name, file_path as fullPath,
                               id_project as projectId, id as docId,  file_extension, created_at,
                               COALESCE(plan_type,'All Other Drawings') as planTypes"))
                    
                ->where(function ($sub_query) use ($data) {
                       
                    if ($data['showFilesForGcComp']) {
                        $sub_query->where('gc_company_id', $data['showFilesForGcComp'])
                                ->where('id_project', $data['project_id']);
                    } 
                    else {
                        $all_projects = ProjectInvitation::allProjectsById($data['projectId']);
                        $sub_query->whereIn('id_project', array_filter($all_projects));
                    }

                    if ($data['docType']) {
                        $sub_query->where('type', $data['docType']);
                    }
                    if ($data['planSubTypes']) {
                        $sub_query->where('plan_type', $data['planSubTypes']);
                    }
                })
                ->groupBy(['name', 'type'])
                ->orderBy($data['sortedBy'], $data['orderBy']);
        
        $all_data_count = $result->get()->toArray();
        $all_data = $result->offset($data['skip'])
                            ->limit($data['limit'])
                            ->get()->toArray();

        $data['all_data_count'] = $all_data_count;
        $data['all_data']= $all_data;
        
        return $data;    
    }
     
}


  