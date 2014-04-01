<?php
class BBC_Sniffs_PHP_MultiByteFunctionsSniff implements PHP_CodeSniffer_Sniff
{

    protected $forbiddenFunctions = array(
                                     'strlen'       => 'mb_strlen',
                                     'strpos'       => 'mb_strpos',
                                     'stripos'      => 'mb_stripos',
                                     'strstr'       => 'mb_strstr',
                                     'stristr'      => 'mb_stristr',
                                     'strrchr'      => 'mb_strrchr',
                                     'strrichr'     => 'mb_strrichr',
                                     'strrpos'      => 'mb_strrpos',
                                     'strripos'     => 'mb_strripos',
                                     'strtolower'   => 'mb_strtolower',
                                     'strtoupper'   => 'mb_strtoupper',
                                     'substr'       => 'mb_substr',
                                     'substr_count' => 'mb_substr_count',
                                     'ucfirst'      => 'mb_convert_case',
                                     'ucwords'      => 'mb_convert_case',
                                    );

    public function register()
    {
        return array(T_STRING);

    }//end register()


    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if (in_array($tokens[$prevToken]['code'], array(T_DOUBLE_COLON, T_OBJECT_OPERATOR, T_FUNCTION)) === true) {
            // Not a call to a PHP function.
            return;
        }

        $function = strtolower($tokens[$stackPtr]['content']);

        if (in_array($function, array_keys($this->forbiddenFunctions)) === false) {
            return;
        }

        $error = "The use of function $function() is forbidden";
        if ($this->forbiddenFunctions[$function] !== null) {
            $error .= '; use '.$this->forbiddenFunctions[$function].'() instead';
        }

        $phpcsFile->addError($error, $stackPtr);

    }//end process()


}//end class

?>