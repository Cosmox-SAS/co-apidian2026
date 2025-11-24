<?php

namespace App\Services;

class PdfTextExtractor
{
    public static function extract($pdfPath)
    {
        $pdftotext = env('PDFTOTEXT_PATH');

        if (!$pdftotext || !file_exists($pdftotext)) {
            throw new \Exception("No se encontró pdftotext. Configure PDFTOTEXT_PATH en .env");
        }

        $cmd = "\"$pdftotext\" \"$pdfPath\" -layout -";
        return shell_exec($cmd);
    }
}
