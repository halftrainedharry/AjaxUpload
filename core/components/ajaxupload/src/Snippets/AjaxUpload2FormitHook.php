<?php
/**
 * AjaxUpload2FormIt Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

use xPDO;

class AjaxUpload2FormitHook extends Hook
{
    /**
     * Get default snippet properties.
     *
     * @return array
     */
    public function getDefaultProperties(): array
    {
        return [
            'debug::bool' => $this->modx->getOption('ajaxupload.debug', null, false),
            'fieldname' => '',
            'fieldformat' => 'csv',
            'target' => '',
            'uid' => '',
            'cacheExpires::int' => $this->modx->getOption('ajaxupload.cache_expires', null, '4'),
            'clearQueue::bool' => false,
            'allowOverwrite::bool' => true,
        ];
    }

    /**
     * Execute the hook and return success.
     *
     * @return string
     * @throws /Exception
     */
    public function execute(): string
    {
        if (!$this->ajaxupload->initialize($this->getProperties())) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not initialize AjaxUpload class.', '', 'AjaxUpload');
            return false;
        }
        if (empty($this->getProperty('fieldname'))) {
            $this->hook->addError($this->getProperty('uid'), 'Missing parameter ajaxuploadFieldname.');
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadFieldname.', '', 'AjaxUpload2Formit');
            return false;
        }
        if (empty($this->getProperty('target'))) {
            $this->hook->addError($this->getProperty('uid'), 'Missing parameter ajaxuploadTarget.');
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadTarget.', '', 'AjaxUpload2Formit');
            return false;
        }

        $errors = $this->ajaxupload->saveUploads($this->getProperty('target'), $this->getProperty('clearQueue'));
        if ($errors) {
            $this->hook->addError($this->getProperty('uid'), $errors);
            return false;
        }

        $this->ajaxupload->deleteExisting();
        $ajaxuploadValue = $this->ajaxupload->getValue($this->getProperty('fieldformat'));
        $this->hook->setValue($this->getProperty('fieldname'), $ajaxuploadValue);
        return true;
    }
}
