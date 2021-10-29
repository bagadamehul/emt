<?php

namespace Enbolt\Emt;

use Illuminate\Database\Eloquent\Model;
use DB;

class ApprovalPrequel extends Model
{
    protected $table = 'approval_prequel';
    protected $guarded = array();
    protected static $moduleName = 'approval_prequel';

    public function getData($filters=[])
    {
        $join_filter              = [];
        $cm_ids                   = 'all';
        $user_id = auth()->user()->id;
        
        $data = self::select(
                        'approval_prequel.*',
                        'admins.name as admin_name',
                        'admins_user.name as user_name'
                    )
                    ->leftjoin('admins',function($j){
                        $j->on('admins.id','=','approval_prequel.approved_by');
                    })
                    ->leftjoin('admins as admins_user',function($j){
                        $j->on('admins_user.id','=','approval_prequel.user_id');
                    })
                    
                    ->orderBy('approval_prequel.created_at','DESC');
        foreach ($join_filter as $key => $join) {
            $data->leftjoin($key, function ($j) use ($join) {
                foreach ($join as $key => $filter) {
                    if ($filter[2] != '') {
                        $j->on($filter[0], $filter[1], $filter[2]);
                    }
                }
            });
        }
        if (!empty($filters)) {
            foreach ($filters as $key => $filter) {
                if (trim($filter[2]) != "") {
                    $data->where($filter[0], $filter[1], $filter[2]);
                }
            }
        }
        //IF AREA MANAGER LOGIN AT THAT TIME DISPLAY ONLY HIS CM's STORE EXECUTIVE's VENDOR
        if ($cm_ids != 'all') {
            $data->whereIn('cm_id', $cm_ids);
        }
        if (isset(request()->search_filter)) {
            $data = $data->searchFilterQuery(request()->search_filter);
        }
        return $data;
    }
    public function getDataApprovalStatusList($filters=[])
    {
        $join_filter              = [];
        $cm_ids                   = 'all';
        $user_id = auth()->user()->id;
        $minusTenDays = \Carbon\Carbon::now()->subDays(10)->format('Y-m-d');
        $data = self::select(
                        'approval_prequel.*',
                        'admins.name as admin_name',
                        'admins_user.name as user_name'
                    )
                    ->leftjoin('admins',function($j){
                        $j->on('admins.id','=','approval_prequel.approved_by');
                    })
                    ->leftjoin('admins as admins_user',function($j){
                        $j->on('admins_user.id','=','approval_prequel.user_id');
                    });
        foreach ($join_filter as $key => $join) {
            $data->leftjoin($key, function ($j) use ($join) {
                foreach ($join as $key => $filter) {
                    if ($filter[2] != '') {
                        $j->on($filter[0], $filter[1], $filter[2]);
                    }
                }
            });
        }
        if (!empty($filters)) {
            foreach ($filters as $key => $filter) {
                if (trim($filter[2]) != "") {
                    $data->where($filter[0], $filter[1], $filter[2]);
                }
            }
        }
        //IF AREA MANAGER LOGIN AT THAT TIME DISPLAY ONLY HIS CM's STORE EXECUTIVE's VENDOR
        if ($cm_ids != 'all') {
            $data->whereIn('cm_id', $cm_ids);
        }
        if (isset(request()->search_filter)) {
            $data = $data->searchFilterQuery(request()->search_filter);
        }
        $data->where(function($query) use($minusTenDays){
            $query->whereDate('approval_prequel.approved_at','>=', $minusTenDays)
                ->orWhereNull('approval_prequel.approved_at');
        });
        return $data;
    }
    public function scopeSearchFilterQuery($query, $searchFilter) {
        return searchFilterQuery($query, $searchFilter);
    }
}
