<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/../third_party/tcpdf/tcpdf.php';

class Pdf extends TCPDF {
    public function __construct() {
        parent::__construct();
    }
}

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf {
    public function createPDF($html, $filename = '', $download = FALSE) {
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        if($download) {
            $dompdf->stream($filename . '.pdf', array('Attachment' => 1));
        } else {
            $dompdf->stream($filename . '.pdf', array('Attachment' => 0));
        }
    }
}

TCPdf from: https://github.com/tecnickcom/TCPDF/releases/download/6.6.2/tcpdf_6_6_2.zip

3. After downloading:
   - Extract the ZIP file
   - Copy all contents from the extracted folder to `c:\laragon\www\koperasi\application\third_party\tcpdf`
   - Make sure the main TCPDF class file (`tcpdf.php`) is directly in that folder

4. Update your PDF library wrapper file:
```php
<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/../third_party/tcpdf/tcpdf.php';

class Pdf extends TCPDF {
    public function __construct() {
        parent::__construct();
    }
}
```