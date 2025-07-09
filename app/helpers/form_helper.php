<?php

class FormHelper
{
    private $submitBntName;
    private $submitBntLabel;
    private $backBntLabel;
    private $formBackLink;
    private $formAction;
    private $method;
    private $enctype;

    public function __construct(
        String $formBackLink,
        String $submitBntName,
        String $submitBntLabel = "Enregistrer",
        String $enctype = 'multipart/form-data',
        String $backBntLabel = "Annuler",
        String $formAction = "",
        String $method = 'post'
    ) {
        $this->submitBntName = $submitBntName;
        $this->submitBntLabel = $submitBntLabel;
        $this->backBntLabel = $backBntLabel;
        $this->formBackLink = $formBackLink;
        $this->formAction = $formAction;
        $this->method = $method;
        $this->enctype  = $enctype;
        $this->formAction  = $formAction;
    }

  


    public function render(
        array $inputs,
        bool $formDisabled = false,
        String $alert = "",
    ): String {
        $disabled = $formDisabled ? 'disabled' : '';
        $formEnfants = "";
        foreach ($inputs as $input) {
            $formEnfants .=  " $input";
        }
        $alertHtml = strlen($alert) < 12 ? '' : '<div class="card-header">' . $alert . '</div>';

        return <<<HTML
    <div class=" mt-1 card">
        <form id="validate_form" action="{$this->formAction}" method="{$this->method}" enctype="{$this->enctype}"     >
                <fieldset {$disabled}>
                        {$alertHtml}
                    <div class="card-body">
                       {$formEnfants}
                    </div>
                    <div class="card-footer">
                        <div class="row ">
                        <a href="{$this->formBackLink}" class="btn btn-outline-dark col mx-2">{$this->backBntLabel}</a>
                        <button type="submit" name="{$this->submitBntName}" class="btn btn-primary col mx-2">{$this->submitBntLabel}</button>
                        </div>
                    </div>
                    </fieldset>
                </form>
                </div>
HTML;
    }



    // $imageData = $_FILES["file"];
    static public function uploadFile($imageData,  int $imageSizeMax = 524880): array
    {
        $imageName = $imageData["name"];
        $imageTmpName = $imageData["tmp_name"];
        $imageSize = $imageData["size"];
        //  $imageType = $imageData["type"];
        if ($imageSize <= $imageSizeMax) {
            $targetRelativePath = 'assets' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . time() . $imageName;
            $target_file = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $targetRelativePath;
            if (move_uploaded_file($imageTmpName, $target_file)) {
                $target_file = $targetRelativePath;
                return  [
                    "data" => $target_file,
                    "error" => null
                ];
            }
        } else {
            return  [
                "data" => null,
                "error" => "Veuillez selectionner une image de taille inférieur à 512 Ko"
            ];
        }
        return  [
            "data" => null,
            "error" => null
        ];
    }

    static function getTargetDirectoryPath(): String
    {
        return  $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'upload';
    }

    static public function alertFeedBack(
        String $hrefFermer,
        bool $isSuccess = false,
        bool $isError = false,
        String $successMessage = "Les données ont  été enregistrée",
        String $errorMessage = "Les données n'ont pas pus être enregistrée",
        String $exceptionMessage = null,

    ): String {
        $resultHtml = "";
        if ($isError) {
            $resultHtml = '  <div class="alert alert-danger">' . $errorMessage . '</div> ';
        }
        if ($isSuccess) {
            $resultHtml = ' <div class="alert alert-success" role="alert" >' . $successMessage . ' <a href="' . $hrefFermer . '" class="alert-link">Fermer</a></div>';
        }
        if (isset($exceptionMessage) && true) {
            $resultHtml = ' <div class="alert alert-waring">' . $exceptionMessage . '</div>';
        }
        return <<<HTML
          {$resultHtml}
HTML;
    }
}
