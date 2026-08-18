<?php

/**
 * Email Template Controller
 *
 * Email Template Controller manages templates of Emails.
 *
 * @category   Enail Template
 * @package    vRent
 * @author     Techvillage Dev Team
 * @copyright  2020 Techvillage
 * @license
 * @version    2.7
 * @link       http://techvill.net
 * @email      support@techvill.net
 * @since      Version 1.3
 * @deprecated None
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{
    Language,
    EmailTemplate
};
use Common;

class EmailTemplateController extends Controller
{

    public function index($id)
    {
        $data['list_menu']  = 'menu-'.$id;
        $data['tempId']     = $id;
        $data['temp_Data']  = EmailTemplate::with(['language' => function ($query) {
            $query->where(['status'=>'Active']);
        }])->where(['type' => 'email','temp_id'=>$id])->get();

        $data['languages'] = Language::where(['language.status'=>'Active'])->orderBy('id')->get();

        return view('admin.emailTemplates.view', $data);
    }

    public function update(Request $request, $id)
    {
        $inputs = $request->except(['_token', '_wysihtml5_mode']);

        foreach ($inputs as $key => $value) {
            if (!is_array($value) || !isset($value['id'])) {
                continue;
            }

            $lang_id = $value['id'];
            unset($value['id']);
            
            $check = EmailTemplate::where('lang_id', '=', $lang_id)
                                    ->where('temp_id', '=', $id)
                                    ->where('type', '=', 'email')
                                    ->first();
            if ($check) {
                $check->subject  = $value['subject'];
                $check->body     = $value['body'];
                $check->save();
            } else {
                $newTemplate = new EmailTemplate;
                $newTemplate->temp_id   = $id;
                $newTemplate->subject   = $value['subject'];
                $newTemplate->body      = $value['body'];
                $newTemplate->lang      = $key;
                $newTemplate->type      = 'email';
                $newTemplate->lang_id   = $lang_id;
                $newTemplate->save();
            }
        }
        Common::one_time_message('success', 'Updated Successfully');
        return redirect("admin/email-template/$id");
    }
}
