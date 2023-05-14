<?php

function upload($file, $uploadLocation, $name = null, $oldFilePath = null)
{
    if ($name == null)
        $name = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

    if ($oldFilePath)
        if (strpos($oldFilePath, 'http') === false)
            unlink($oldFilePath);

    $fileName = str_replace(' ', '_', $name) . '.' . $file->getClientOriginalExtension();
    $file->move($uploadLocation, $fileName);
    return $uploadLocation . '/' . $fileName;
}
