<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class HomeworkFile extends Model
{
    protected $guarded = [];

    public static function fromUpload(UploadedFile $f): self
    {
        return static::create([
            'name' => $f->getClientOriginalName(),
            'mime' => $f->getMimeType() ?: 'application/octet-stream',
            'size' => $f->getSize(),
            'data' => base64_encode(file_get_contents($f->getRealPath())),
        ]);
    }

    public function download()
    {
        return response()->streamDownload(fn () => print(base64_decode($this->data)), $this->name,
            ['Content-Type' => $this->mime ?: 'application/octet-stream']);
    }
}
