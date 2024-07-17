<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class VerificationMailLog extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'verification_mail_log';
    protected $fillable = ['email', 'verification_code', 'ip', 'device_id', 'browser', 'os', 'is_verified', 'expires_at'];
}
