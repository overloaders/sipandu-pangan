<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WbpDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'wbp_id', 'jenis_dokumen', 'nomor_dokumen', 'nama_dokumen',
        'file_path', 'tanggal_dokumen', 'keterangan',
    ];

    protected $casts = ['tanggal_dokumen' => 'date'];

    public function wbp() { return $this->belongsTo(Wbp::class); }
}
