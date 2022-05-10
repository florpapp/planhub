<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectInvitation extends Model
{
    protected $table = 'gc_project_invitation_mail_sent';

   protected $fillable = [
        'gc_project_invitation_mail_sent_id',
        'project_id',
        'emailId',
        'is_merged',
        'is_approved_merge',
        'merged_project_id',//I assume that when the project is merged it has a new id
        'is_original_gc',
        'id_company',
        'user_id',
        'merge_date',
        'merge_aproved_date',
        'created_at',
        'updated_at'
        
    ];
    use HasFactory;


    public static function getProjectMergedByIdEmail($project_id, $user_email){

        $project = ProjectInvitation::where('project_id', $project_id)
                    ->where('emailId', $user_email)
                    ->where(function ($subquery) {
                        $subquery->orWhere('is_merged', true)
                                ->orWhere('is_approved_merge', true);
                    })->first();
        return $project; 
    }
    
    public static function getFileForGeneralContractorComp($project_id){
        $gcForFile = ProjectInvitation::where('project_id', $project_id)
                        ->where('gc_project_invitation_mail_sent.is_original_gc', true)
                        ->leftjoin('user_profile', 'user_profile.id_user', 'gc_project_invitation_mail_sent.user_id')
                        ->first();          
        return $gcForFile;
    }

    /*
    * Returns the unique merged_project_id of all de projects by id
    */
    public static function allProjectsById($project_id){

        $all_projects = ProjectInvitation::where('project_id', $project_id)
                        ->pluck('merged_project_id')
                        ->unique()
                        ->values()
                        ->toArray();
                       
        //pluck returns an array of merged_project_id
        
        return $all_projects;
    }

    
   
}
