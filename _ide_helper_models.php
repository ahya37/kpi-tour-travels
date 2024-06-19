<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $job_employee_id
 * @property int $members
 * @property string $label
 * @property int $periode
 * @property string|null $is_sinkronisasi
 * @property string $notes
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial query()
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial whereIsSinkronisasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial whereJobEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial whereMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial wherePeriode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlumniProspekMaterial whereUpdatedBy($value)
 */
	class AlumniProspekMaterial extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $level_name
 * @property string $level_type
 * @property string $level_status
 * @method static \Illuminate\Database\Eloquent\Builder|ApsLevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApsLevel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApsLevel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApsLevel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsLevel whereLevelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsLevel whereLevelStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsLevel whereLevelType($value)
 */
	class ApsLevel extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $menu_nodeid
 * @property int|null $menu_parent_id
 * @property string|null $menu_parent_nodeid
 * @property string|null $menu_type Parent, Child, Single
 * @property string|null $menu_name
 * @property string|null $menu_desc
 * @property string|null $menu_metta
 * @property string|null $menu_content
 * @property string|null $menu_icon
 * @property string $menu_level_type
 * @property string|null $menu_status
 * @property int|null $menu_order
 * @property string|null $menu_route
 * @property string|null $menu_create
 * @property string $menu_update
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuLevelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuMetta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuNodeid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuParentNodeid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApsMenus whereMenuUpdate($value)
 */
	class ApsMenus extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ApsPrevilege newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApsPrevilege newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApsPrevilege query()
 */
	class ApsPrevilege extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign query()
 */
	class Campaign extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $alumni_prospect_material_id
 * @property int $id_members
 * @property string|null $name
 * @property string|null $telp
 * @property string|null $provinsi
 * @property string|null $kota
 * @property string|null $kecamatan
 * @property string|null $kelurahan
 * @property string|null $alamat
 * @property string|null $address
 * @property string|null $is_respone
 * @property string|null $reason_id
 * @property string|null $tourcode
 * @property string|null $tourcode_haji
 * @property string|null $tourcode_tourmuslim
 * @property string|null $notes
 * @property string|null $remember
 * @property string|null $is_sinkronisasi
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial query()
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereAlumniProspectMaterialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereIdMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereIsRespone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereIsSinkronisasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereKecamatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereKelurahan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereKota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereProvinsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereRemember($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereTelp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereTourcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereTourcodeHaji($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereTourcodeTourmuslim($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailAlumniProspekMaterial whereUpdatedBy($value)
 */
	class DetailAlumniProspekMaterial extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $marketing_target_id
 * @property string $program_id
 * @property int $month_number angka urutan bulan
 * @property string $month_name nama bulan
 * @property int $target total target dari detail target marketing
 * @property int $realization total realisasi dari detail target marketing
 * @property int $difference total selisih dari detail target marketing
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget query()
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget whereDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget whereMarketingTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget whereMonthName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget whereMonthNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget whereRealization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DetailMarketingTarget whereUpdatedBy($value)
 */
	class DetailMarketingTarget extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $employee_id
 * @property string $group_division_id
 * @property string $name
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Division newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Division newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Division query()
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereGroupDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereUpdatedBy($value)
 */
	class Division extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property int $user_id
 * @property string $name
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUserId($value)
 */
	class Employee extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string|null $roles_id
 * @property string $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $is_active
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|GroupDivision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupDivision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupDivision query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupDivision whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupDivision whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupDivision whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupDivision whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupDivision whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupDivision whereRolesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupDivision whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupDivision whereUpdatedBy($value)
 */
	class GroupDivision extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $employee_id
 * @property string $sub_division_id
 * @property string $group_division_id
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|JobEmployee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobEmployee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobEmployee query()
 * @method static \Illuminate\Database\Eloquent\Builder|JobEmployee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobEmployee whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobEmployee whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobEmployee whereGroupDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobEmployee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobEmployee whereSubDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobEmployee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobEmployee whereUpdatedBy($value)
 */
	class JobEmployee extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Market newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Market newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Market query()
 */
	class Market extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $year
 * @property int|null $total_target
 * @property int|null $total_realization
 * @property int|null $total_difference
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingTarget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingTarget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingTarget query()
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingTarget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingTarget whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingTarget whereTotalDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingTarget whereTotalRealization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingTarget whereTotalTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingTarget whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingTarget whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketingTarget whereYear($value)
 */
	class MarketingTarget extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedBy($value)
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string $product_id
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Program newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Program newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Program query()
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereUpdatedBy($value)
 */
	class Program extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $uuid
 * @property string $pkb_title
 * @property string $pkb_start_date
 * @property string|null $pkb_end_date
 * @property string|null $pkb_description
 * @property string $pkb_pkt_id
 * @property string $pkb_employee_id
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan wherePkbDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan wherePkbEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan wherePkbEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan wherePkbPktId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan wherePkbStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan wherePkbTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerBulanan whereUuid($value)
 */
	class ProkerBulanan extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $uid
 * @property int|null $parent_id
 * @property string|null $pkt_title
 * @property string|null $pkt_description
 * @property string|null $pkt_year
 * @property string $pkt_pic_job_employee_id
 * @property string $division_group_id
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan whereDivisionGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan wherePktDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan wherePktPicJobEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan wherePktTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan wherePktYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProkerTahunan whereUpdatedBy($value)
 */
	class ProkerTahunan extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Reason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reason newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reason query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reason whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reason whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reason whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reason whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reason whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reason whereUpdatedBy($value)
 */
	class Reason extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Recipient query()
 */
	class Recipient extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string|null $name
 * @property string $division_group_id
 * @property string $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SubDivision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubDivision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubDivision query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubDivision whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubDivision whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubDivision whereDivisionGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubDivision whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubDivision whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubDivision whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubDivision whereUpdatedBy($value)
 */
	class SubDivision extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $idx
 * @property string $date
 * @property string $group_division_id
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPlan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPlan whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPlan whereGroupDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPlan whereIdx($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPlan whereUpdatedBy($value)
 */
	class WorkPlan extends \Eloquent {}
}

