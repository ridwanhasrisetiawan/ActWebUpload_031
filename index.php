<!DOCTYPE html>
<html>
<head>
    <title>Upload File Lengkap</title>

    <style>

        #preview{
            margin-top:20px;
        }

        #preview img{
            width:200px;
            border:1px solid black;
            padding:5px;
        }

    </style>

</head>
<body>

<h2>Upload File</h2>

<form action="upload.php"
method="post"
enctype="multipart/form-data">

Pilih File:

<input
type="file"
name="fileToUpload"
id="fileToUpload"
onchange="previewFile()">

<br><br>

<div id="preview">

Belum ada file dipilih

</div>

<br>

<input
type="submit"
value="Upload"
name="submit">

</form>

<hr>

<h2>Hasil Upload</h2>

<?php

$folder="uploads/";

if(is_dir($folder)){

$files=scandir($folder);

foreach($files as $file){

if($file!="." && $file!=".."){

echo "<div>";

$ext=strtolower(
pathinfo(
$file,
PATHINFO_EXTENSION
));

if(in_array(
$ext,
['jpg','jpeg','png','gif']
)){

echo "<img src='uploads/$file'
width='150'><br>";

}

echo "<b>$file</b><br>";

echo "
<a href='uploads/$file' download>
Download
</a>

|

<a href='delete.php?file=$file'
onclick='return confirm(
\"Hapus file?\")'>
Delete
</a>

";

echo "</div><hr>";

}

}

}

?>

<script>

function previewFile(){

let file=
document.getElementById(
"fileToUpload"
).files[0];

let preview=
document.getElementById(
"preview"
);

preview.innerHTML="";

if(!file){

preview.innerHTML=
"Belum ada file dipilih";

return;

}

let tipe=file.type;

if(tipe.startsWith("image/")){

let reader=
new FileReader();

reader.onload=function(e){

preview.innerHTML=
`
<h3>Preview:</h3>

<img src="${e.target.result}">

<p>
Nama:
${file.name}
</p>

<p>
Ukuran:
${(file.size/1024).toFixed(2)}
KB
</p>
`;

}

reader.readAsDataURL(file);

}
else{

preview.innerHTML=
`
<h3>Preview File</h3>

<p>
Nama:
${file.name}
</p>

<p>
Ukuran:
${(file.size/1024).toFixed(2)}
KB
</p>

<p>
File bukan gambar
</p>
`;

}

}

</script>

</body>
</html>