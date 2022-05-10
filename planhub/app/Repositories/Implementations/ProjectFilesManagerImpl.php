<?php
namespace App\Repositories\Implementations;

use App\Repositories\Contracts\ProjectFilesManager;
use App\Models\ProjectFiles;
use App\Models\ProjectInvitation;

/**
 *
 */
class ProjectFilesManagerImpl implements ProjectFilesManager
{

  function __construct()
  {
  }
    //CHECKED
    public function checkProjectMerged($data, $userData){
       /* 
         * Ckecks if project is merged and re aranges data
        */
    
        $mergedProject = ProjectInvitation::getProjectMergedByIdEmail($data['projectId'], $userData['email']);
        if ($mergedProject) {
            $data['projectId'] = json_decode($mergedProject->merged_project_id);
            //$data['projectId'] = $mergedProject['merged_project_id'];
        }
        if (empty($data['showFilesForGcComp'])) {
            $gcForFile = ProjectInvitation::getFileForGeneralContractorComp($data['projectId']);
            $data['showFilesForGcComp'] = (!empty($gcForFile)) ? $gcForFile->id_company:"";
            //$data['showFilesForGcComp'] = (!empty($gcForFile)) ? $gcForFile['id_company']:"";
        }
        
        return $data;
        
    }


    public function getsortedFiles($data, $userData){
        
        $result = array();
        if (isset($userData['is_user']) && ($userData['is_user'] == 'General Contractor')) {
            $result = ProjectFiles::getSortedFilesGeneralContractor($data, $userData);
        } else {
            $result = ProjectFiles::getSortedFiles($data, $userData);
        }
        $all_data_count = $result['all_data_count']; //returns all the data of the whole table
        $all_data = $result['all_data'];//returns all the data with an offset
    
        $column_types_total = array();
        
        if (!empty($all_data_count)) {
            $column_types_total = array_count_values(array_column($all_data_count, 'docType'));        
        }
    
        return array(
            'message' => ApiConstant::DATA_FOUND,
            'data' => !empty($all_data)? $all_data:null,//returns an array or null
            'allcount' => !empty($all_data_count)?count($all_data_count):0,
            'countplans' => isset($column_types_total['plans'])? $column_types_total['plans']:0,
            'countspecs' => isset($column_types_total['specs'])? $column_types_total['specs']:0,
            'countgeneral' => isset($column_types_total['general'])? $column_types_total['general']:0,
            'countaddendums' => isset($column_types_total['addendums'])? $column_types_total['addendums']:0,
            'countRFI_response' => isset($column_types_total['RFI_response'])? $column_types_total['RFI_response']:0
        );

    }
	
}
