<?php
class BBC_Sniffs_PHP_LintCheckSniff implements PHP_CodeSniffer_Sniff
{

    public function register()
    {
        return array(T_OPEN_TAG);

    }
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $prevOpenTag = $phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1));
        if ($prevOpenTag !== false) {
            return;
        }

        $fileName = $phpcsFile->getFilename();

        $cmd = 'php -l '.$fileName.' 2>&1';

        $exitCode = exec($cmd, $output, $retval);

        if (true === is_array($output)) {
            $msg = join("\n", $output);
        }

        if (true === is_numeric($exitCode) && 0 < $exitCode) {
            throw new PHP_CodeSniffer_Exception("Failed invoking php -l, exitcode was [$exitCode], retval was [$retval], output was [$msg]");
        }

        if ( 0 < $retval )
        {
            $error = 'Failed PHP lint check';
            if ( 0 < PHP_CODESNIFFER_VERBOSITY )
            {
                $error .= ":\n".$msg;
            }
            $phpcsFile->addError($error, $stackPtr);
        }

    }

}