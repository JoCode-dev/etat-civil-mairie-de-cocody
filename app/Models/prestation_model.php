<?php
class PrestationModel {
    private $id;
    private $indicateurpays;
    private $date;
    private $time;
    private $reference;
    private $quantite;
    private $prix;
    private $lat;
    private $lon;
    private $clientid;
    private $clientname;
    private $clientmobile;
    private $clientmobilemoney;
    private $artisanid;
    private $artisancontact;
    private $artisannom;
    private $serviceid;
    private $statut;
    private $description;
    private $observationclient;

    // Constructor
    public function __construct($id = null, $indicateurpays = 225, $date = null, $time = null, $reference = null, $quantite = null, $prix = null, $lat = null, $lon = null, $clientid = null, $clientname = null, $clientmobile = null, $clientmobilemoney = null, $artisanid = null, $artisancontact = null, $artisannom = null, $serviceid = null, $statut = 'enattente', $description = null, $observationclient = null) {
        $this->id = $id;
        $this->indicateurpays = $indicateurpays;
        $this->date = $date;
        $this->time = $time;
        $this->reference = $reference;
        $this->quantite = $quantite;
        $this->prix = $prix;
        $this->lat = $lat;
        $this->lon = $lon;
        $this->clientid = $clientid;
        $this->clientname = $clientname;
        $this->clientmobile = $clientmobile;
        $this->clientmobilemoney = $clientmobilemoney;
        $this->artisanid = $artisanid;
        $this->artisancontact = $artisancontact;
        $this->artisannom = $artisannom;
        $this->serviceid = $serviceid;
        $this->statut = $statut;
        $this->description = $description;
        $this->observationclient = $observationclient;
    }

    static public function fromArray($row): PrestationModel {
        return new PrestationModel(
            array_key_exists('id', $row) ? $row['id'] : null,
            $row['indicateurpays'] ?? 225,
            $row['date'], 
            $row['time'],
            $row['reference'],
            $row['quantite'],
            $row['prix'],
            $row['lat'],
            $row['lon'],
            $row['clientid'],
            $row['clientname'],
            $row['clientmobile'],
            $row['clientmobilemoney'],
            $row['artisanid'],
            $row['artisancontact'],
            $row['artisannom'],
            $row['serviceid'],
            $row['statut'] ?? 'En attente',
            $row['description'],
            $row['observationclient']
        );
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getIndicateurpays() {
        return $this->indicateurpays;
    }

    public function setIndicateurpays($indicateurpays) {
        $this->indicateurpays = $indicateurpays;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getTime() {
        return $this->time;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function getReference() {
        return $this->reference;
    }

    public function setReference($reference) {
        $this->reference = $reference;
    }

    public function getQuantite() {
        return $this->quantite;
    }

    public function setQuantite($quantite) {
        $this->quantite = $quantite;
    }

    public function getPrix() {
        return $this->prix;
    }

    public function setPrix($prix) {
        $this->prix = $prix;
    }

    public function getLat() {
        return $this->lat;
    }

    public function setLat($lat) {
        $this->lat = $lat;
    }

    public function getLon() {
        return $this->lon;
    }

    public function setLon($lon) {
        $this->lon = $lon;
    }

    public function getClientid() {
        return $this->clientid;
    }

    public function setClientid($clientid) {
        $this->clientid = $clientid;
    }

    public function getClientname() {
        return $this->clientname;
    }

    public function setClientname($clientname) {
        $this->clientname = $clientname;
    }

    public function getClientmobile() {
        return $this->clientmobile;
    }

    public function setClientmobile($clientmobile) {
        $this->clientmobile = $clientmobile;
    }

    public function getClientmobilemoney() {
        return $this->clientmobilemoney;
    }

    public function setClientmobilemoney($clientmobilemoney) {
        $this->clientmobilemoney = $clientmobilemoney;
    }

    public function getArtisanid() {
        return $this->artisanid;
    }

    public function setArtisanid($artisanid) {
        $this->artisanid = $artisanid;
    }

    public function getArtisancontact() {
        return $this->artisancontact;
    }

    public function setArtisancontact($artisancontact) {
        $this->artisancontact = $artisancontact;
    }

    public function getArtisannom() {
        return $this->artisannom;
    }

    public function setArtisannom($artisannom) {
        $this->artisannom = $artisannom;
    }

    public function getServiceid() {
        return $this->serviceid;
    }

    public function setServiceid($serviceid) {
        $this->serviceid = $serviceid;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getObservationclient() {
        return $this->observationclient;
    }

    public function setObservationclient($observationclient) {
        $this->observationclient = $observationclient;
    }

    function formValide(): bool {
        return empty($this->getErreur());
    }

    function getErreur(): array {
        $erreurs = [];
        if (strlen($this->reference) < 3) {
            $erreurs["reference"] = "La référence est trop courte!";
        }
        if ($this->quantite <= 0) {
            $erreurs["quantite"] = "La quantité doit être supérieure à 0!";
        }
        if ($this->prix <= 0) {
            $erreurs["prix"] = "Le prix doit être supérieur à 0!";
        }
        return $erreurs;
    }

    function getDescriptionHtmlspecialchars(): string {
        return htmlspecialchars(isset($this->description) ? $this->description : "");
    }
}
?>
