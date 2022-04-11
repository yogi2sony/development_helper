<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Lead;
use App\Models\Lead_stage;
use App\Models\Lead_stage_log;
use App\Models\Lead_next_activitie;
use App\Models\Mst_class_color;
use App\Models\Lead_next_activity_log;
use App\Models\Lead_next_activity_message;
use App\Models\Lead_meeting_log;
use App\Models\Lead_contact_person;
use App\Models\Lead_account_transaction;
use App\Models\Lead_converted_module;
use App\Models\Lead_source;
use App\Models\Lead_status;
use App\Models\Lead_assigned_member;
use App\Models\Lead_log;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Invoice_item;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Companie;
use App\Models\Term;
use App\Models\View_lead;
use App\Models\Event_action_log;
use App\Models\Mail_action_log;
use App\Mail\Taskmail;
use App\Mail\TaskReassignmail;
use App\Models\Todo_comment;
use App\Models\Lead_column;
use App\Models\Developer_reminder;
use App\User;
use Mail;
//use App\Mail\UserRequest;
use Auth;
use Carbon\Carbon;
//use Input;
use Browser;
use Schema;
use Redirect;
use Image;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\File;
//use Illuminate\Support\Facades\Storage;


class LeadpipelineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*Lead_next_activitie::select('id','name','class')->where('is_delete', 0)->orderBy('id')->chunk(10000, function ($items) {
        foreach ($items as $key => $value) {
            $countLead = Lead::where('is_delete', '=', '0')->where('status', '=', $value->id)->count('id');
            $items[$key]["cld"]=$countLead;
        }
    });*/

    public function index(){
        Carbon::now('Asia/Kolkata');
        $nowdt = Carbon::now();
        $now = $nowdt->toDateTimeString();
        $today0 = date("Y-m-d ");
        $today2 = new DateTime("now");
        $today = Carbon::today();
        $today1 = $today->toDateTimeString();
        $xtomorrow = Carbon::tomorrow();
        $tomorrow = $xtomorrow->toDateTimeString();
        $xyesterday = Carbon::yesterday();
        $yesterday = $xyesterday->toDateTimeString();
        $xnext30days = $today->addDays(30);
        $next30days = $xnext30days->toDateTimeString();
        /*Carbon::now()->subDays(30)*/
        $xlast30days = $nowdt->subDays(30);
        $last30days = $xlast30days->toDateTimeString();
        $date = Carbon::today();
        $date->addDays(2);
        $date = $date->toDateTimeString();
        /*dd($today1);*/
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        $items = [];
        /*time id: today1:25, tomorrow:26, yesterday:27, next30days:28, */
        /*==== if admin =============================*/
        $developer_reminders = Developer_reminder::where('id', '=', '1')->where('is_delete', '=', '0')->where('is_active', '=', '1')->get();
        $drcheck_at = $developer_reminders[0]['check_at'];
        /*dd($developer_reminders[0]['check_at']);*/
        if ($siurole == 1) { 
            $totallead = Lead::where('is_delete', '=', '0')->count('id');
            $items = Lead_next_activitie::select('id','name','class')->where('is_delete', '=', '0')->orderBy('sortby', 'asc')->get();
        //====== Admin ================================================================
            foreach ($items as $key => $value) { 
                /*Start time effect in lead*/
                if ($drcheck_at != $today1) { 
                    if ($value->id == 25) { 
                        /*=============================================*/
                        Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->where('next_activity', '!=', '2')->where('next_activity', '!=', '12')->whereDate('next_activity_at', '=', $today1)->chunk(20000, function ($txitems) {
                            foreach ($txitems as $key => $txval) { 
                                // dd($txval['id']); 
                                $tx = $txval['next_activity'];
                                if($tx != 25) { 
                                    $txid = $txval['id'];
                                    $txitem = Lead::find($txid);
                                    $txitem->next_activity = 25;
                                    $txitem->save();
                                }
                            }
                        });
                        /*=============================================*/
                        /*$countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $today1)->count('id');
                        $items[$key]["cld"]=$countLead;*/
                    }
                    if ($value->id == 26) { 
                        /*=============================================*/
                        Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->where('next_activity', '!=', '2')->where('next_activity', '!=', '12')->whereDate('next_activity_at', '=', $tomorrow)->chunk(20000, function ($mxitems) {
                            foreach ($mxitems as $key => $mxval) { 
                                $mx = $mxval['next_activity'];
                                if($mx != 26) { 
                                    $mxid=$mxval['id'];
                                    $mxitem = Lead::find($mxid);
                                    $mxitem->next_activity = 26;
                                    $mxitem->save();
                                }
                            }
                        });
                        /*=============================================*/
                        /*$countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $tomorrow)->count('id');
                        $items[$key]["cld"]=$countLead;*/
                    }
                    if ($value->id == 27) { 
                        /*=============================================*/
                        Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->where('next_activity', '!=', '2')->where('next_activity', '!=', '12')->whereDate('next_activity_at', '=', $yesterday)->chunk(20000, function ($yxitems) {
                            foreach ($yxitems as $key => $yxval) { 
                                $yx = $yxval['next_activity'];
                                if($yx != 27) { 
                                    $yxid=$yxval['id'];
                                    $yxitem = Lead::find($yxid);
                                    $yxitem->next_activity = 27;
                                    $yxitem->save();
                                }
                            }
                        });
                        /*=============================================*/
                        /*$countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $yesterday)->count('id');
                        $items[$key]["cld"]=$countLead;*/
                    }
                    if ($value->id == 28) { 
                        /*=============================================*/
                        Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->where('next_activity', '!=', '2')->where('next_activity', '!=', '12')->whereBetween('next_activity_at', [$date, $next30days])->chunk(20000, function ($nx30items) { 
                            foreach ($nx30items as $key => $nx30val) { 
                                $nx30 = $nx30val['next_activity'];
                                if($nx30 != 28) { 
                                    $id=$nx30val['id'];
                                    $nx30item = Lead::find($id);
                                    $nx30item->next_activity = 28;
                                    $nx30item->save();
                                }
                            }
                        });
                        /*=============================================*/
                        /*$countLead = Lead::where('is_delete', '=', '0')->whereBetween('next_activity_at', [$date, $next30days])->count('id');
                        $items[$key]["cld"]=$countLead;*/
                        /*$countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at','>=', $today)->whereDate('next_activity_at','<=', $next30days)->count('id');*/
                    }
                    if ($value->id == 11) { 
                        /*=============================================*/
                        Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->where('next_activity', '!=', '2')->where('next_activity', '!=', '12')->whereDate('next_activity_at','>', $next30days)->orwhere('next_activity', '=', 25)->orwhere('next_activity', '=', 26)->orwhere('next_activity', '=', 27)->orwhere('next_activity', '=', 28)->chunk(20000, function ($nx11items) { 
                            foreach ($nx11items as $key => $nx11val) { 
                                $nx11 = $nx11val['next_activity'];
                                if($nx11 != 11) { 
                                    $id=$nx11val['id'];
                                    $nx30item = Lead::find($id);
                                    $nx30item->next_activity = 11;
                                    $nx30item->save();
                                }
                            }
                        });
                        /*=============================================*/
                        /*$countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at','>', $next30days)->orwhere('next_activity', '=', 11)->count('id');
                        $items[$key]["cld"]=$countLead;*/
                    }
                    $dritem = Developer_reminder::find(1);
                    $dritem->check_at = $today1;
                    $dritem->updated_at = new DateTime("now");
                    $dritem->save();
                } else { /*Pass*/ }
                /*End time effect in lead*/

                /*Leads Cont Start*/
                /*if ($value->id != 25 && $value->id != 26 && $value->id != 27 && $value->id != 28) { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('next_activity', '=', $value->id)->count('id');
                    $items[$key]["cld"]=$countLead;
                }*/
                $countLead = Lead::where('is_delete', '=', '0')->where('next_activity', '=', $value->id)->count('id');
                $items[$key]["cld"]=$countLead;
                /*Leads Cont End*/
            }
        //====== End Admin ================================================================
        }else { 
            $totallead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->count('id');
            $items = Lead_next_activitie::select('id','name','class')->where('is_delete', '=', '0')->orderBy('sortby', 'asc')->get();
            /*===Not Admin==============================================*/
            foreach ($items as $key => $value) { 
                if ($drcheck_at != $today1) {
                    if ($value->id == 25) { 
                        /*=============================================*/
                        Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->where('next_activity', '!=', '2')->where('next_activity', '!=', '12')->whereDate('next_activity_at', '=', $today1)->chunk(20000, function ($xitems) {
                            foreach ($xitems as $key => $val) { 
                                $nextactivity = $val['next_activity'];
                                if($nextactivity != 25) { 
                                    $id=$val['id'];
                                    $itemnx = Lead::find($id);
                                    $itemnx->next_activity = 25;
                                    $itemnx->save();
                                }
                            }
                        });
                        /*=============================================*/
                        /*$countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereDate('next_activity_at', '=', $today1)->count('id');
                        $items[$key]["cld"]=$countLead;*/
                    }
                    if ($value->id == 26) { 
                        /*=============================================*/
                        Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->where('next_activity', '!=', '2')->where('next_activity', '!=', '12')->whereDate('next_activity_at', '=', $tomorrow)->chunk(20000, function ($xitems) {
                            foreach ($xitems as $key => $val) { 
                                $nextactivity = $val['next_activity'];
                                if($nextactivity != 26) { 
                                    $id=$val['id'];
                                    $itemnx = Lead::find($id);
                                    $itemnx->next_activity = 26;
                                    $itemnx->save();
                                }
                            }
                        });
                        /*=============================================*/
                        /*$countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereDate('next_activity_at', '=', $tomorrow)->count('id');
                        $items[$key]["cld"]=$countLead;*/
                    }
                    if ($value->id == 27) { 
                        /*=============================================*/
                        Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->where('next_activity', '!=', '2')->where('next_activity', '!=', '12')->whereDate('next_activity_at', '=', $yesterday)->chunk(20000, function ($xitems) {
                            foreach ($xitems as $key => $val) { 
                                $nextactivity = $val['next_activity'];
                                if($nextactivity != 27) { 
                                    $id=$val['id'];
                                    $itemnx = Lead::find($id);
                                    $itemnx->next_activity = 27;
                                    $itemnx->save();
                                }
                            }
                        });
                        /*=============================================*/
                        /*$countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereDate('next_activity_at', '=', $yesterday)->count('id');
                        $items[$key]["cld"]=$countLead;*/
                    }
                    if ($value->id == 28) { 
                        /*=============================================*/
                        Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->where('next_activity', '!=', '2')->where('next_activity', '!=', '12')->whereBetween('next_activity_at', [$date, $next30days])->chunk(20000, function ($xitems) { 
                            foreach ($xitems as $key => $val) { 
                                $nextactivity = $val['next_activity'];
                                if($nextactivity != 28) { 
                                    $id=$val['id'];
                                    $itemnx = Lead::find($id);
                                    $itemnx->next_activity = 28;
                                    $itemnx->save();
                                }
                            }
                        });
                        /*=============================================*/
                        /*$countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereBetween('next_activity_at', [$date, $next30days])->count('id');
                        $items[$key]["cld"]=$countLead;*/
                        /*$countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at','>=', $today)->whereDate('next_activity_at','<=', $next30days)->count('id');*/
                    }
                    if ($value->id == 11) { 
                        /*=============================================*/
                        Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->where('next_activity', '!=', '2')->where('next_activity', '!=', '12')->where('assign_id', '=', $siuid)->whereDate('next_activity_at','>', $next30days)->orwhere('next_activity', '=', 25)->orwhere('next_activity', '=', 26)->orwhere('next_activity', '=', 27)->orwhere('next_activity', '=', 28)->chunk(20000, function ($nx11items) { 
                            foreach ($nx11items as $key => $nx11val) { 
                                $nx11 = $nx11val['next_activity'];
                                if($nx11 != 11) { 
                                    $id=$nx11val['id'];
                                    $nx30item = Lead::find($id);
                                    $nx30item->next_activity = 11;
                                    $nx30item->save();
                                }
                            }
                        });
                        /*=============================================*/
                        /*$countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereDate('next_activity_at','>', $next30days)->orwhere('next_activity', '=', 11)->count('id');
                        $items[$key]["cld"]=$countLead;*/
                    }
                    $dritem = Developer_reminder::find(1);
                    $dritem->check_at = $today1;
                    $dritem->updated_at = new DateTime("now");
                    $dritem->save();
                } else { /*Pass*/ }
                /*Leads Cont Start*/
                /*if ($value->id != 25 && $value->id != 26 && $value->id != 27 && $value->id != 28) { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->where('next_activity', '=', $value->id)->count('id');
                    $items[$key]["cld"]=$countLead;
                }*/
                $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->where('next_activity', '=', $value->id)->count('id');
                $items[$key]["cld"]=$countLead;
                /*Leads Cont End*/
            }
            /*=== End if loop ==========================================*/
        }
        /*==== end if admin =============================*/
        $countcolors = Mst_class_color::where('is_delete', '=', '0')->count('id');
        $bgcolors = Mst_class_color::select('id','name')->where('is_delete', '=', '0')->get();
        //dd($items);
        return view('admin.lead.leadpipeline',compact('items','totallead','bgcolors','countcolors'));
    }

    public function xxindex(){
        Carbon::now('Asia/Kolkata');
        $nowdt = Carbon::now();
        $now = $nowdt->toDateTimeString();
        $today0 = date("Y-m-d ");
        $today2 = new DateTime("now");
        $today = Carbon::today();
        $today1 = $today->toDateTimeString();
        $xtomorrow = Carbon::tomorrow();
        $tomorrow = $xtomorrow->toDateTimeString();
        $xyesterday = Carbon::yesterday();
        $yesterday = $xyesterday->toDateTimeString();
        $xnext30days = $today->addDays(30);
        $next30days = $xnext30days->toDateTimeString();
        $date = Carbon::today();
        $date->addDays(2);
        $date = $date->toDateTimeString();
        /*dd($date);*/
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        $items = [];
        /*time id: today1:25, */
        /*==== if admin =============================*/
        if ($siurole == 1) { 
            $totallead = Lead::where('is_delete', '=', '0')->count('id');
            $items = Lead_next_activitie::select('id','name','class')->where('is_delete', '=', '0')->orderBy('sortby', 'asc')->get();
            echo count($items);
            //exit();
            foreach ($items as $key => $value) { 
                if ($value->id == 25) { 
                    $countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $today1)->count('id');
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $today1)->chunk(10000, function ($txitems) {
                        foreach ($txitems as $key => $txval) { 
                            // dd($txval['id']); 
                            $tx = $txval['next_activity'];
                            if($tx != 25) { 
                                $txid = $txval['id'];
                                $txitem = Lead::find($txid);
                                $txitem->next_activity = 25;
                                $txitem->save();
                            }
                        }
                    });
                    /*=============================================*/
                }elseif ($value->id == 26) { 
                    $countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $tomorrow)->count('id');
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $tomorrow)->chunk(10000, function ($mxitems) {
                        foreach ($mxitems as $key => $mxval) { 
                            $mx = $mxval['next_activity'];
                            if($mx != 26) { 
                                $mxid=$mxval['id'];
                                $mxitem = Lead::find($mxid);
                                $mxitem->next_activity = 26;
                                $mxitem->save();
                            }
                        }
                    });
                    /*=============================================*/
                }elseif ($value->id == 27) { 
                    $countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $yesterday)->count('id');
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $yesterday)->chunk(10000, function ($yxitems) {
                        foreach ($yxitems as $key => $yxval) { 
                            $yx = $yxval['next_activity'];
                            if($yx != 27) { 
                                $yxid=$yxval['id'];
                                $yxitem = Lead::find($yxid);
                                $yxitem->next_activity = 27;
                                $yxitem->save();
                            }
                        }
                    });
                    /*=============================================*/
                }elseif ($value->id == 28) { 
                    $countLead = Lead::where('is_delete', '=', '0')->whereBetween('next_activity_at', [$date, $next30days])->count('id');
                    /*$countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at','>=', $today)->whereDate('next_activity_at','<=', $next30days)->count('id');*/
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereBetween('next_activity_at', [$date, $next30days])->chunk(10000, function ($nx30items) { 
                        foreach ($nx30items as $key => $nx30val) { 
                            $nx30 = $nx30val['next_activity'];
                            if($nx30 != 28) { 
                                $id=$nx30val['id'];
                                $nx30item = Lead::find($id);
                                $nx30item->next_activity = 28;
                                $nx30item->save();
                            }
                        }
                    });
                    /*=============================================*/
                }else { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('next_activity', '=', $value->id)->count('id');
                    $items[$key]["cld"]=$countLead;
                }
            }
        }else { 
            $totallead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->count('id');
            $items = Lead_next_activitie::select('id','name','class')->where('is_delete', '=', '0')->orderBy('sortby', 'asc')->get();
            /*===Not Admin==============================================*/
            foreach ($items as $key => $value) { 
                if ($value->id == 25) { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereDate('next_activity_at', '=', $today1)->count('id');
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $today1)->chunk(10000, function ($xitems) {
                        foreach ($xitems as $key => $val) { 
                            $nextactivity = $val['next_activity'];
                            if($nextactivity != 25) { 
                                $id=$val['id'];
                                $itemnx = Lead::find($id);
                                $itemnx->next_activity = 25;
                                $itemnx->save();
                            }
                        }
                    });
                    /*=============================================*/
                }elseif ($value->id == 26) { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereDate('next_activity_at', '=', $tomorrow)->count('id');
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $tomorrow)->chunk(10000, function ($xitems) {
                        foreach ($xitems as $key => $val) { 
                            $nextactivity = $val['next_activity'];
                            if($nextactivity != 26) { 
                                $id=$val['id'];
                                $itemnx = Lead::find($id);
                                $itemnx->next_activity = 26;
                                $itemnx->save();
                            }
                        }
                    });
                    /*=============================================*/
                }elseif ($value->id == 27) { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereDate('next_activity_at', '=', $yesterday)->count('id');
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $yesterday)->chunk(10000, function ($xitems) {
                        foreach ($xitems as $key => $val) { 
                            $nextactivity = $val['next_activity'];
                            if($nextactivity != 27) { 
                                $id=$val['id'];
                                $itemnx = Lead::find($id);
                                $itemnx->next_activity = 27;
                                $itemnx->save();
                            }
                        }
                    });
                    /*=============================================*/
                }elseif ($value->id == 28) { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereBetween('next_activity_at', [$date, $next30days])->count('id');
                    /*$countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at','>=', $today)->whereDate('next_activity_at','<=', $next30days)->count('id');*/
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereBetween('next_activity_at', [$date, $next30days])->chunk(10000, function ($xitems) { 
                        foreach ($xitems as $key => $val) { 
                            $nextactivity = $val['next_activity'];
                            if($nextactivity != 28) { 
                                $id=$val['id'];
                                $itemnx = Lead::find($id);
                                $itemnx->next_activity = 28;
                                $itemnx->save();
                            }
                        }
                    });
                    /*=============================================*/
                }else { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->where('next_activity', '=', $value->id)->count('id');
                    $items[$key]["cld"]=$countLead;
                }
            }
            /*===End Not Admin==========================================*/
            /*foreach ($items as $key => $value) { 
                $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->where('next_activity', '=', $value->id)->count('id');
                $items[$key]["cld"]=$countLead;
            }*/
        }
        /*==== end if admin =============================*/
        $countcolors = Mst_class_color::where('is_delete', '=', '0')->count('id');
        $bgcolors = Mst_class_color::select('id','name')->where('is_delete', '=', '0')->get();
        //dd($items);
        return view('admin.lead.leadpipeline',compact('items','totallead','bgcolors','countcolors'));
    }

    public function xxxindex(){
        Carbon::now('Asia/Kolkata');
        $nowdt = Carbon::now();
        $now = $nowdt->toDateTimeString();
        $today0 = date("Y-m-d ");
        $today2 = new DateTime("now");
        $today = Carbon::today();
        $today1 = $today->toDateTimeString();
        $xtomorrow = Carbon::tomorrow();
        $tomorrow = $xtomorrow->toDateTimeString();
        $xyesterday = Carbon::yesterday();
        $yesterday = $xyesterday->toDateTimeString();
        $xnext30days = $today->addDays(30);
        $next30days = $xnext30days->toDateTimeString();
        $date = Carbon::today();
        $date->addDays(2);
        $date = $date->toDateTimeString();
        /*dd($date);*/
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        $items = [];
        /*==== if admin =============================*/
        if ($siurole == 1) { 
            $totallead = Lead::where('is_delete', '=', '0')->count('id');
            $items = Lead_next_activitie::select('id','name','class')->where('is_delete', '=', '0')->orderBy('sortby', 'asc')->get();
            foreach ($items as $key => $value) { 
                if ($value->id == 25) { 
                    $countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $today1)->count('id');
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $today1)->chunk(10000, function ($txitems) {
                        foreach ($txitems as $key => $txval) { 
                            // dd($txval['id']); 
                            $tx = $txval['next_activity'];
                            if($tx != 25) { 
                                $txid = $txval['id'];
                                $txitem = Lead::find($txid);
                                $txitem->next_activity = 25;
                                $txitem->save();
                            }
                        }
                    });
                    /*=============================================*/
                }elseif ($value->id == 26) { 
                    $countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $tomorrow)->count('id');
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $tomorrow)->chunk(10000, function ($mxitems) {
                        foreach ($mxitems as $key => $mxval) { 
                            $mx = $mxval['next_activity'];
                            if($mx != 26) { 
                                $mxid=$mxval['id'];
                                $mxitem = Lead::find($mxid);
                                $mxitem->next_activity = 26;
                                $mxitem->save();
                            }
                        }
                    });
                    /*=============================================*/
                }elseif ($value->id == 27) { 
                    $countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $yesterday)->count('id');
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $yesterday)->chunk(10000, function ($yxitems) {
                        foreach ($yxitems as $key => $yxval) { 
                            $yx = $yxval['next_activity'];
                            if($yx != 27) { 
                                $yxid=$yxval['id'];
                                $yxitem = Lead::find($yxid);
                                $yxitem->next_activity = 27;
                                $yxitem->save();
                            }
                        }
                    });
                    /*=============================================*/
                }elseif ($value->id == 28) { 
                    $countLead = Lead::where('is_delete', '=', '0')->whereBetween('next_activity_at', [$date, $next30days])->count('id');
                    /*$countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at','>=', $today)->whereDate('next_activity_at','<=', $next30days)->count('id');*/
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereBetween('next_activity_at', [$date, $next30days])->chunk(10000, function ($nx30items) { 
                        foreach ($nx30items as $key => $nx30val) { 
                            $nx30 = $nx30val['next_activity'];
                            if($nx30 != 28) { 
                                $id=$nx30val['id'];
                                $nx30item = Lead::find($id);
                                $nx30item->next_activity = 28;
                                $nx30item->save();
                            }
                        }
                    });
                    /*=============================================*/
                }else { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('next_activity', '=', $value->id)->count('id');
                    $items[$key]["cld"]=$countLead;
                }
            }
        }else { 
            $totallead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->count('id');
            $items = Lead_next_activitie::select('id','name','class')->where('is_delete', '=', '0')->orderBy('sortby', 'asc')->get();
            /*===Not Admin==============================================*/
            foreach ($items as $key => $value) { 
                if ($value->id == 25) { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereDate('next_activity_at', '=', $today1)->count('id');
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $today1)->chunk(10000, function ($xitems) {
                        foreach ($xitems as $key => $val) { 
                            $nextactivity = $val['next_activity'];
                            if($nextactivity != 25) { 
                                $id=$val['id'];
                                $itemnx = Lead::find($id);
                                $itemnx->next_activity = 25;
                                $itemnx->save();
                            }
                        }
                    });
                    /*=============================================*/
                }elseif ($value->id == 26) { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereDate('next_activity_at', '=', $tomorrow)->count('id');
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $tomorrow)->chunk(10000, function ($xitems) {
                        foreach ($xitems as $key => $val) { 
                            $nextactivity = $val['next_activity'];
                            if($nextactivity != 26) { 
                                $id=$val['id'];
                                $itemnx = Lead::find($id);
                                $itemnx->next_activity = 26;
                                $itemnx->save();
                            }
                        }
                    });
                    /*=============================================*/
                }elseif ($value->id == 27) { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereDate('next_activity_at', '=', $yesterday)->count('id');
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereDate('next_activity_at', '=', $yesterday)->chunk(10000, function ($xitems) {
                        foreach ($xitems as $key => $val) { 
                            $nextactivity = $val['next_activity'];
                            if($nextactivity != 27) { 
                                $id=$val['id'];
                                $itemnx = Lead::find($id);
                                $itemnx->next_activity = 27;
                                $itemnx->save();
                            }
                        }
                    });
                    /*=============================================*/
                }elseif ($value->id == 28) { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->whereBetween('next_activity_at', [$date, $next30days])->count('id');
                    /*$countLead = Lead::where('is_delete', '=', '0')->whereDate('next_activity_at','>=', $today)->whereDate('next_activity_at','<=', $next30days)->count('id');*/
                    $items[$key]["cld"]=$countLead;
                    /*=============================================*/
                    Lead::select('id','name','next_activity')->where('is_delete', '=', '0')->whereBetween('next_activity_at', [$date, $next30days])->chunk(10000, function ($xitems) { 
                        foreach ($xitems as $key => $val) { 
                            $nextactivity = $val['next_activity'];
                            if($nextactivity != 28) { 
                                $id=$val['id'];
                                $itemnx = Lead::find($id);
                                $itemnx->next_activity = 28;
                                $itemnx->save();
                            }
                        }
                    });
                    /*=============================================*/
                }else { 
                    $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->where('next_activity', '=', $value->id)->count('id');
                    $items[$key]["cld"]=$countLead;
                }
            }
            /*===End Not Admin==========================================*/
            /*foreach ($items as $key => $value) { 
                $countLead = Lead::where('is_delete', '=', '0')->where('assign_id', '=', $siuid)->where('next_activity', '=', $value->id)->count('id');
                $items[$key]["cld"]=$countLead;
            }*/
        }
        /*==== end if admin =============================*/
        $countcolors = Mst_class_color::where('is_delete', '=', '0')->count('id');
        $bgcolors = Mst_class_color::select('id','name')->where('is_delete', '=', '0')->get();
        //dd($items);
        return view('admin.lead.leadpipeline',compact('items','totallead','bgcolors','countcolors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $leadid= $id;
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        $lead_columns = Lead_column::select('id','name','namefield')->where('development_key', '=', '0')->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $itemarray = Lead_column::where('is_active', '=', '1')->where('development_key', '=', '0')->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->pluck('namefield')->toArray();
        //$itemarray = array('id','name','company','company_mobile','company_email','company_cpname','company_cpmobile','company_cpemail','company_cprole','contact_person_mobile','next_activity_id','next_activity','next_activity_message','authority','assign','status_id','status');
        if ($siurole == 1) {
            $items = View_lead::select($itemarray)->where('next_activity_id', '=', $id)->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(100);
            $countlead = Lead::where('next_activity', '=', $id)->where('is_delete', '=', '0')->count();
        } else {
            $items = View_lead::select($itemarray)->where('next_activity_id', '=', $id)->where('assign_id', '=', $siuid)->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->simplePaginate(100);
            $countlead = Lead::where('next_activity', '=', $id)->where('assign_id', '=', $siuid)->where('is_delete', '=', '0')->count();
        };
        if ($countlead) {
            $result = 'Total records found: '.$countlead;
        }else{
            $result = 'Record doesn\'t exist.';
        };
        //dd($items);
        $next_activities = Lead_next_activitie::find($id);
        $title = $next_activities->name;
        $viewid = 2;
        return view('admin.lead.index',compact('items','title','viewid','countlead','result','leadid','lead_columns'));
        /*return view('admin.lead.leadpipeline_show',compact('items','title','viewid','countlead','result','leadid'));*/
    }

    public function searchpipeLeaddata(Request $request){
        $siuid = Auth::user()->id;
        $siurole = Auth::user()->role;
        if($request->has('q')){
            $search = $request->q;
            $items = View_lead::where('is_delete', '=', '0')->where('name','LIKE',"%$search%")->orwhere('company','LIKE',"%$search%")->orwhere('contact_person_mobile','LIKE',"%$search%")->orderBy('created_at', 'desc')->paginate(100);
            $countlead = Lead::where('is_delete', '=', '0')->where('name','LIKE',"%$search%")->orwhere('company','LIKE',"%$search%")->orwhere('contact_person_mobile','LIKE',"%$search%")->count('id');
        }else{
            $items = View_lead::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->paginate(100);
            $countlead = Lead::where('is_delete', '=', '0')->count('id');
        }
        $title = 'Search Result Leads';
        return view('admin.lead.leadpipeline_show',compact('items','title'));
        return view('admin.lead.index',compact('items','title','lead_id','countlead'));
    }

    public function leadPipelineDetail($id){
        $item = View_lead::find($id);
        $meeting_logs = Lead_meeting_log::where('is_delete', '=', '0')->where('lead_id', '=', $id)->orderBy('created_at', 'desc')->get();
        $next_activity_logs = Lead_next_activity_log::where('is_delete', '=', '0')->where('lead_id', '=', $id)->orderBy('created_at', 'desc')->get();
        $last_meetings = Lead_meeting_log::where('is_delete', '=', '0')->where('lead_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $last_next_activitys = Lead_next_activity_log::where('is_delete', '=', '0')->where('lead_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $next_activities = Lead_next_activitie::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $customers = Customer::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $employees = Employee::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $statuss = Lead_status::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $stageid = $item->stage_id;
        $stages = Lead_stage::find($stageid);
        //dd($stageid);
        $contact_persons = Lead_contact_person::where('is_delete', '=', '0')->where('lead_id', '=', $id)->orderBy('created_at', 'desc')->get();
        $amount_trns = Lead_account_transaction::where('is_delete', '=', '0')->where('lead_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $invoices = Invoice::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $second_last_next_activitys = Lead_next_activity_log::where('is_delete', '=', '0')->where('lead_id', '=', $id)->orderBy('created_at', 'desc')->skip(1)->take(1)->get('name');

        $sources = Lead_source::where('is_delete', '=', '0')->orderBy('created_at', 'asc')->get();
        $companies = Companie::where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();
        $users = User::all();
        $assigned_members = Lead_assigned_member::where('lead_id', '=', $id)->where('is_delete', '=', '0')->orderBy('assign_at', 'desc')->get();
        $lead_logs = Lead_log::where('lead_id', '=', $id)->where('is_delete', '=', '0')->orderBy('created_at', 'desc')->get();

        $companyid = $item->company_id;
        $company_data = Companie::find($companyid);
        // get previous user id
        $previous = View_lead::where('id', '<', $id)->max('id');
        // get next user id
        $next = View_lead::where('id', '>', $id)->min('id');
        return view('admin.lead.leadpipeline_detail',compact('item','customers','employees','statuss','stages','meeting_logs','next_activity_logs','next_activities','last_meetings','last_next_activitys','contact_persons','amount_trns','invoices','second_last_next_activitys','sources','companies','users','assigned_members','lead_logs','previous','next','company_data'));
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
