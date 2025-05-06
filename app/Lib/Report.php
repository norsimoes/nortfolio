<?php

namespace Lib;

use Exception;
use Error;

/**
 * Report
 *
 * Exception and error handler class.
 */
class Report
{
    protected Exception|Error|null $exception = null;

    /**
     * Class constructor
     */
    function __construct($exception = null)
    {
        $this->exception = $exception;
    }

    /**
     * Render error page
     *
     * Fill the error page template and display it to the user.
     *
     * @throws \Exception
     */
    public function renderErrorPage(string $type): void
    {
        $data = [
            'type' => $type,
            'message' => $this->exception->getMessage(),
            'code' => $this->exception->getCode(),
            'file' => str_replace(APP_BASEPATH_APP, '', $this->exception->getFile()),
            'line' => $this->exception->getLine(),
            'trace' => $this->exception->getTrace(),
        ];

        $template = new \Lib\Template();

        $template->loadView('Error/Generic', $data);
        $template->render('Error');
    }

}
