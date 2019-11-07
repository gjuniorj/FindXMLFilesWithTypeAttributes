<?php

    $extension_to_search = ".xml";

    $directory_to_search = getFilePath();

    $elementsArray = searchFileWithExtension($directory_to_search,$extension_to_search, 'getXMLTypeAttribute');
    createFile();
    writeFile ($elementsArray);
    echo 'File created!'.PHP_EOL;


    /**
     * Goes inside given directory and searches for files with specified extension.
     * Returns an array of elements from a callback function.
     *
     * @param $dir
     * @param $extension_to_search
     * @param $callback
     * @return array
     */
    function searchFileWithExtension($dir, $extension_to_search, $callback){
        $typesArray = array();

        //Returns an array with directories and file names found inside given directory
        $files = scandir($dir);

        //Go inside the directory searching for the file with the given extension
        foreach($files as $file){

            //Gets full path of directory or file
            $path = realpath($dir.DIRECTORY_SEPARATOR.$file);

            //Checks if path is a file or directory
            if(!is_dir($path)) {

                //It's a file. Searches for a file with specified extension.
                if(strcmp($extension_to_search, getFileExtension($file)) == 0){
                        array_push($typesArray,  $callback($path));
                }

            } //It's a directory.
        elseif($file != "." && $file != "..") {

                //Searches file inside found directory
                array_push($typesArray, searchFileWithExtension($path, $extension_to_search, $callback));
            }
        }
        return $typesArray;

    }


    /**
     * Returns file extension.
     *
     * @param string $haystack
     * @return string
     */
    function getFileExtension($haystack){

        $needlePos = strrpos($haystack,'.');

        //Verifies if file has an extension
        if ( $needlePos === false ){
            return '';
        }

        $fileExtension =  substr($haystack, $needlePos);

       return $fileExtension;

    }


    /**
     * Iterates each field tag and returns an array of attributes of "type" type.
     *
     * @param $xmlFilePath
     * @return array
     */
    function getXMLTypeAttribute($xmlFilePath){

        $typesArray = array();
        $countElementsArray = array();

        // Creates an object that provides recursive iteration over all nodes of a SimpleXMLElement object
        // Parameter "data_is_url" must be "true" because the object is created from a xml file path, and not a string (see first parameter of function)
        $xmlIterator = new SimpleXMLIterator($xmlFilePath, null, true);
        //Constructs a recursive iterator from an iterator
        $recursive = new RecursiveIteratorIterator($xmlIterator,RecursiveIteratorIterator::SELF_FIRST);

        foreach ($recursive as $tag => $attribute) {

            //Searches for field tag
            if ($tag === 'field') {

                //Get type attribute
                $type = $attribute['type'];

                //Write type attribute in array, if attribute is not null and if it's not in array
                if ( !(is_null($type)) ) {
                    //Write file path in array
                    array_push($typesArray, $xmlFilePath);
                }
            }

        }

        if ( !empty($typesArray) ){
            $countElementsArray = array_count_values($typesArray);

            //Prints the elements quantities in terminal
            print_r($countElementsArray);
        }

        return $countElementsArray;

    }

    /**
     * Writes an array to a file
     * @param $array
     *
     */
    function writeFile ($array){

        foreach ($array as $names => $quantities){

                    if ( !is_array($quantities) ){
                        file_put_contents('file.txt', $names . PHP_EOL, FILE_APPEND);
                    }
                    elseif (is_array($quantities)){
                        writeFile($quantities);
                    }
        }
    }

    /**
     * Creates a blank file.
     */
    function createFile(){

        //Verify if file exists
        if (file_exists('file.txt')){
            //Delete file
            unlink('file.txt');
        }

        //Creates file
        $fp = fopen('file.txt','w+');
        fclose($fp);

    }

/**
 * Gets a valid path from user
 * @return string
 */
    function getFilePath(){

        $path = readline('Input directory path to search for files: ');

        //Verifies if path is a real directory
        while ( !(is_dir($path)) ){
            echo "Type a valid directory path.";

            $path = readline('Input directory path to search for files: ');
        }

        return $path;

    }