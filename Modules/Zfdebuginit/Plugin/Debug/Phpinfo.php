<?php

class Modules_Zfdebuginit_Plugin_Debug_Phpinfo extends ZFDebug_Controller_Plugin_Debug_Plugin implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface {
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'phpinfo';

    /**
     * Create ZFDebug_Controller_Plugin_Debug_Plugin_Html
     *
     * @param string $tab
     * @param string $panel
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Gets identifier for this plugin
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * Returns the base64 encoded icon
     *
     * @return string
     **/
    public function getIconData()
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NTUxRkZFN0FDNjFEMTFFNjkwMDNEODM2QkJEMzkxQzMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NTUxRkZFN0JDNjFEMTFFNjkwMDNEODM2QkJEMzkxQzMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpBNTg5NDZGRkM2MUMxMUU2OTAwM0Q4MzZCQkQzOTFDMyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpBNTg5NDcwMEM2MUMxMUU2OTAwM0Q4MzZCQkQzOTFDMyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PhVCP6sAAAS7SURBVHjaxFdLbBtVFL3+j+3YTt18TBolTmijhqY0TWgXQS0uG5CqogaJLmBDxQbEonSHkPhE3bBLWbCmIKEghPh0AWUBTUEghAiK1EQVUZo4CUncxPV/PLbHH+69M5Pajh3baVCvdDVvZt68c+55b967V1coFEAznU4H5fbmuz8248WH/gz6oOrNUN2m0f3ot9AnP77y3HR5hxLMagQQmEAvoZ8v/thoNILBYNi6z2ZzJQPn87lyPCLzKfpVJBOpSQCBvdj8RI2aAa1WAd0KJpMJ9Hp9yeihUAJSqQS+E7CviZ8RiVwuiy4jQVnrSuBjSOJqMaaxTG6S9yZJTMBOpxPsdltFnfP5PJOR5TQDkio0MAWh1xvYTSYLPstDJiNhvwxN2zhiHMPrRW0cfdm4DE6Rtre3bQOXZRkikQisrwdAFJNMQpZTHH0ul4NkMgrpdJJBH6iqB4vFDoJg1x69WjxmOQGOvK2tdZvUweB9CATu8ZxryiQSCX5HkRoMRjCbrSy7KEZ5CkrXjhmJbFeznABLWQ5OZjYr85tOp7cGj8eVuSfZCViZjjyvhUpj0LSUmxHqNIq6qamJoyYQIkJTZTRa1L8hw0oQOMler9VNQIlAz0Q0oz8jHBa5LQhNsBvTwyO2XRPIxmbh9xsfwj5r+NEQWLw7A1NTU2AU3A9FYMc1EFz4Gubm5kqeDZ44C7b9R2Hz3iLfX//iCjgcDuXdyVFu//rTZyXfNDlb4fDxCyDmGlDA02KFiYkJyMgF6Ow+wk5kvvx8HJ543MnRH31yCA71n4TungG+n/7zG1hamOW2u7UbPJ39UNAJcOvmDdhY/qMxBWLRTb72HhyArv5z3O5bUgaPhle23jk8z4LLJvF5Q6CJRJDfnTj1CqwGRPB0LMPff/2GgSTB3sgaiEc2+Hqg64gio82IW/A6dHR0gH9xvuRdeHOJr+79XRDCdl9fHwRDKX6Wwa2ZNzKTrTEFVpdn+Xrn9iQ4XbOwMD8Da2tr8NLLb4B/QSHQ0u6F2IoEofvLfO/1dsL31+d4HazO/8DPfvn5O77vPDgC4Vi+8SmIoRLkZpMOXnv9PWj2HIO1f74Cn88Hct6MPSRo2SfA8PAwPNbRg9tznAED/97h74eeehoOD74AMZHErpOA2aSH1ZW7LKXv3Ds4fw8+pHZL74voAImkeuA4TsPAyGm4PaOAnjpzAQT30NY3qWyDv6HbZeEovD2HSsBrmWDKKkoc6IGwtLt9IIJnfnMgKMHI8283vKkkCr2oxKWq4HRg1foLvqWTjpKNvTbKlujIrkVgjGXArIeI7KVpuQLataoEMGGkDHaMOlIGtFdGCQzlhWpiennHjQhJfED5PCUcoVD4ocEpYaWsWbVRLTWvtROOUoEhiiIrsdvpoEUnSXGtDriI4JN1bcUqyzOkhCRJsLGxyRlxI0bZcSolEnhEjfxapX66Okqzcby8RW2Xy1mSkvE5oKZkpZKLWoXkV8Gnq5VmNRMS/PiyOiWRaDTGNQGtj0q/GUWdTMY0cIr4eKXasCEFyorUca2wsFgsrIgoKuk4FSjqWP5q891QcboDEaoZ39dqx+JdFP2j4iL0fyFQROS8SsRbL3AlAv8JMACRykxkGjJcdwAAAABJRU5ErkJggg==';
    }

    /**
     * Gets menu tab for the Debugbar
     *
     * @return string
     */
    public function getTab()
    {
        return '<a href="/mvc/Zfdebuginit/index/phpinfo/" target="_blank">Phpinfo</a>';
    }

    /**
     * Gets content panel for the Debugbar
     *
     * @return string
     */
    public function getPanel() {  }

}
