<?php

namespace myLibs;

use Nette;

/**
* sends pdf to browser trying to show it instead of downloading it
*
* based on Nette Framework 2.0.10
*/

class PdfResponse extends \Nette\Application\Responses\FileResponse {
    
    /**
* Sends response to output.
* @return void
*/
    public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse)
    {
        $httpResponse->setContentType($this->contentType);
        $httpResponse->setHeader('Content-Disposition', 'inline; filename="' . $this->name . '"');

        $filesize = $length = filesize($this->file);
        $handle = fopen($this->file, 'r');

        if ($this->resuming) {
            $httpResponse->setHeader('Accept-Ranges', 'bytes');
            if (preg_match('#^bytes=(\d*)-(\d*)\z#', $httpRequest->getHeader('Range'), $matches)) {
                list(, $start, $end) = $matches;
                if ($start === '') {
                    $start = max(0, $filesize - $end);
                    $end = $filesize - 1;

                } elseif ($end === '' || $end > $filesize - 1) {
                    $end = $filesize - 1;
                }
                if ($end < $start) {
                    $httpResponse->setCode(416); // requested range not satisfiable
                    return;
                }

                $httpResponse->setCode(206);
                $httpResponse->setHeader('Content-Range', 'bytes ' . $start . '-' . $end . '/' . $filesize);
                $length = $end - $start + 1;
                fseek($handle, $start);

            } else {
                $httpResponse->setHeader('Content-Range', 'bytes 0-' . ($filesize - 1) . '/' . $filesize);
            }
        }

        $httpResponse->setHeader('Content-Length', $length);
        while (!feof($handle) && $length > 0) {
            echo $s = fread($handle, min(4e6, $length));
            $length -= strlen($s);
        }
        fclose($handle);
    }
}