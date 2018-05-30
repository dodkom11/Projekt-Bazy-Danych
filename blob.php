<?php
    /* Este script demuestra la subida de ficheros a columnas LOB.
     * El campo de formulario usado para este ejemplo es como este:
     * <form action="upload.php" method="post" enctype="multipart/form-data">
     * <input type="file" name="lob_upload" />
     * ...
     */


 
?>

<form action="ok.php" method="post" enctype="multipart/form-data">
Upload file:
<input type="file" name="lob_upload" />

<input type="submit" value="Upload" />
<input type="reset" value="Reset" />
</form>



