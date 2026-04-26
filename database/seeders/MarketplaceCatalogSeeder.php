<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\CategoryAttributeOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarketplaceCatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->catalog() as $i => $node) {
            $root = $this->cat($node['name'], null, $i);
            $this->children($root, $node['children'] ?? []);
        }
    }

    private function catalog(): array
    {
        return [
            ['name' => 'Laptop, Tablete & Telefoane', 'children' => [
                ['name' => 'Laptopuri si accesorii', 'children' => ['Laptopuri', 'Laptopuri gaming', 'Laptopuri business', 'Laptopuri 2 in 1', 'MacBook', 'Genti laptop', 'Docking stations', 'Standuri si coolere laptop', 'Incarcatoare laptop', 'Baterii laptop', 'Tastaturi laptop', 'Hard disk-uri notebook', 'Memorii laptop']],
                ['name' => 'Telefoane mobile si accesorii', 'children' => ['Telefoane mobile', 'Telefoane cu butoane', 'Huse telefoane', 'Folii si sticla protectie', 'Incarcatoare telefoane', 'Cabluri telefoane', 'Power bank', 'Suporturi auto telefon', 'Selfie stick-uri']],
                ['name' => 'Tablete si accesorii', 'children' => ['Tablete', 'Tablete grafice', 'Huse tablete', 'Folie protectie tableta', 'Tastaturi pentru tablete', 'Stylus', 'Docking si suporturi tablete']],
                ['name' => 'Wearables si gadgeturi', 'children' => ['Smartwatch-uri', 'Bratari fitness', 'Casti true wireless', 'Trackere GPS']],
            ]],
            ['name' => 'PC, Periferice & Software', 'children' => [
                ['name' => 'Desktop PC & Monitoare', 'children' => ['Desktop PC', 'Desktop PC gaming', 'Sisteme office', 'Monitoare', 'Monitoare gaming', 'Accesorii monitoare', 'Mini PC', 'All-in-One PC']],
                ['name' => 'Componente PC', 'children' => ['Procesoare', 'Placi de baza', 'Placi video', 'Memorii RAM', 'SSD-uri interne', 'Hard disk-uri interne', 'Surse PC', 'Carcase', 'Coolere procesoare', 'Coolere si ventilatoare carcasa', 'Pasta termoconductoare', 'Placi de sunet', 'Placi de retea']],
                ['name' => 'Periferice PC', 'children' => ['Mouse', 'Mouse gaming', 'Tastaturi', 'Tastaturi gaming', 'Casti PC', 'Microfoane PC', 'Webcam-uri', 'Boxe PC', 'Mouse pad-uri', 'Tablete grafice', 'Cititoare carduri', 'Hub-uri USB', 'Joystick-uri si controllere']],
                ['name' => 'Stocare externa si retea', 'children' => ['Hard disk-uri externe', 'SSD-uri externe', 'Stick-uri USB', 'Carduri de memorie', 'NAS-uri', 'Routere wireless', 'Access point-uri', 'Switch-uri', 'Camere supraveghere IP', 'UPS-uri']],
                ['name' => 'Software', 'children' => ['Sisteme de operare', 'Office si productivitate', 'Antivirus si securitate', 'Software educatie']],
            ]],
            ['name' => 'TV, Audio-Video & Foto', 'children' => [
                ['name' => 'Televizoare si accesorii', 'children' => ['Televizoare LED', 'Televizoare QLED', 'Televizoare OLED', 'Suporturi TV', 'Cabluri si adaptoare AV', 'Media player-e']],
                ['name' => 'Audio hi-fi si home cinema', 'children' => ['Soundbar-uri', 'Boxe portabile', 'Boxe shelf si floorstanding', 'Sisteme audio complete', 'Amplificatoare si receivere', 'Subwoofere', 'Casti on-ear si over-ear', 'Casti in-ear', 'Microfoane']],
                ['name' => 'Foto video', 'children' => ['Aparate foto mirrorless', 'Aparate foto DSLR', 'Aparate foto compacte', 'Aparate instant', 'Camere video sport', 'Drone', 'Obiective', 'Trepiede', 'Gimbal-uri', 'Blituri si lumini foto']],
                ['name' => 'Proiectoare si e-readere', 'children' => ['Videoproiectoare', 'Ecrane proiectie', 'eBook readere']],
                ['name' => 'Console gaming', 'children' => ['Console', 'Jocuri consola', 'Accesorii console', 'Gamepad-uri', 'Volane gaming', 'Scaune gaming', 'Ochelari VR']],
            ]],
            ['name' => 'Electrocasnice & Climatizare', 'children' => [
                ['name' => 'Frigidere si congelare', 'children' => ['Combine frigorifice', 'Frigidere', 'Side by side', 'Lazi frigorifice', 'Congelatoare']],
                ['name' => 'Masini de spalat si uscare', 'children' => ['Masini de spalat rufe', 'Masini de spalat cu uscator', 'Uscatoare de rufe', 'Masini de spalat vase']],
                ['name' => 'Gatit si bucatarie', 'children' => ['Aragazuri', 'Cuptoare incorporabile', 'Plite incorporabile', 'Hote incorporabile', 'Cuptoare cu microunde', 'Espressoare', 'Blendere', 'Mixere', 'Roboti de bucatarie', 'Fierbatoare', 'Toastere', 'Air fryer']],
                ['name' => 'Curatenie si ingrijire locuinta', 'children' => ['Aspiratoare', 'Aspiratoare verticale', 'Aspiratoare robot', 'Aparate de curatat cu abur', 'Fiare de calcat', 'Statii de calcat']],
                ['name' => 'Climatizare', 'children' => ['Aparate de aer conditionat', 'Purificatoare de aer', 'Dezumidificatoare', 'Umidificatoare', 'Ventilatoare si racitoare de aer', 'Aeroterme', 'Calorifere electrice']],
            ]],
            ['name' => 'Gaming, Carti & Birotica', 'children' => [
                ['name' => 'Gaming', 'children' => ['Console gaming', 'Accesorii gaming', 'Jocuri PC', 'Jocuri PlayStation', 'Jocuri Xbox', 'Jocuri Nintendo', 'Scaune gaming']],
                ['name' => 'Carti', 'children' => ['Fictiune', 'Dezvoltare personala', 'Business si management', 'Istorie', 'Psihologie', 'Carti pentru copii', 'Benzi desenate']],
                ['name' => 'Birotica si papetarie', 'children' => ['Imprimante laser', 'Imprimante inkjet', 'Cartuse si tonere', 'Distrugatoare documente', 'Laminatoare', 'Hartie copiator', 'Caiete', 'Pixuri si stilouri', 'Markere si evidentiatoare', 'Ghiozdane si genti', 'Penare']],
            ]],
            ['name' => 'Fashion', 'children' => [
                ['name' => 'Femei', 'children' => ['Rochii', 'Bluze femei', 'Tricouri femei', 'Camasi femei', 'Pantaloni femei', 'Jeansi femei', 'Fuste', 'Hanorace femei', 'Geci femei', 'Lenjerie femei', 'Costume de baie femei', 'Incaltaminte femei', 'Genti femei', 'Bijuterii femei']],
                ['name' => 'Barbati', 'children' => ['Tricouri barbati', 'Camasi barbati', 'Hanorace barbati', 'Pantaloni barbati', 'Jeansi barbati', 'Sacouri', 'Geci barbati', 'Lenjerie barbati', 'Incaltaminte barbati', 'Portofele si curele', 'Ceasuri barbati']],
                ['name' => 'Copii', 'children' => ['Haine copii', 'Incaltaminte copii', 'Accesorii copii', 'Ghiozdane copii']],
                ['name' => 'Sport fashion', 'children' => ['Treninguri', 'Pantofi sport', 'Rucsacuri sport', 'Ochelari de soare']],
            ]],
            ['name' => 'Ingrijire personala & Cosmetice', 'children' => [
                ['name' => 'Ten si corp', 'children' => ['Demachiere si curatare', 'Creme fata', 'Seruri', 'Masti ten', 'SPF', 'Geluri de dus', 'Lotiuni si creme corp', 'Deodorante']],
                ['name' => 'Machiaj si parfumuri', 'children' => ['Fond de ten', 'Rujuri', 'Mascara', 'Farduri si palete', 'Parfumuri femei', 'Parfumuri barbati', 'Seturi cadou']],
                ['name' => 'Par si styling', 'children' => ['Sampon', 'Balsam', 'Masti de par', 'Vopsea de par', 'Uscatoare de par', 'Placi de par', 'Ondulatoare', 'Perii electrice']],
                ['name' => 'Igiena si aparate personale', 'children' => ['Pasta de dinti', 'Periute de dinti', 'Periute electrice', 'Aparate de ras', 'Aparate de tuns', 'Epilatoare']],
                ['name' => 'Wellness si vitamine', 'children' => ['Vitamine si suplimente', 'Articole wellness', 'Aparate si dispozitive medicale']],
            ]],
            ['name' => 'Casa, Gradina & Bricolaj', 'children' => [
                ['name' => 'Mobila si saltele', 'children' => ['Mobila living', 'Mobila dormitor', 'Mobila bucatarie', 'Mobila birou', 'Mobila hol', 'Saltele']],
                ['name' => 'Decoratiuni si textile', 'children' => ['Perne si pilote', 'Lenjerii de pat', 'Covoare', 'Perdele si draperii', 'Corpuri de iluminat', 'Decoratiuni interioare']],
                ['name' => 'Bucatarie si servire', 'children' => ['Vase pentru gatit', 'Seturi de farfurii', 'Tacamuri', 'Cutii depozitare', 'Accesorii servire']],
                ['name' => 'Gradinarit si terasa', 'children' => ['Mobilier de gradina', 'Gratare si accesorii', 'Unelte de gradinarit', 'Aparate de spalat cu presiune', 'Ghivece si suporturi', 'Plante si seminte']],
                ['name' => 'Scule si materiale', 'children' => ['Bormasini si rotopercutoare', 'Polizoare si slefuitoare', 'Surubelnite electrice', 'Truse de scule', 'Generatoare', 'Vopsea si tencuieli', 'Materiale de constructii']],
                ['name' => 'Sanitare si incalzire', 'children' => ['Baterii sanitare', 'Mobilier baie', 'Cabine de dus si coloane', 'Boilere', 'Radiatoare', 'Centrale termice']],
            ]],
            ['name' => 'Sport & Travel', 'children' => [
                ['name' => 'Fitness si nutritie', 'children' => ['Benzi de alergat', 'Biciclete fitness', 'Gantere si seturi', 'Banci fitness', 'Saltele fitness', 'Nutritie sportiva']],
                ['name' => 'Camping si drumetie', 'children' => ['Corturi camping', 'Sac de dormit', 'Mobilier camping', 'Arzatoare outdoor', 'Rucsacuri drumetie', 'Lanterne', 'Cutite si bricege']],
                ['name' => 'Ciclism si mobilitate', 'children' => ['Biciclete copii', 'Biciclete oras', 'Biciclete electrice', 'Accesorii biciclete', 'Trotinete', 'Trotinete electrice', 'Hoverboard-uri']],
                ['name' => 'Articole sportive', 'children' => ['Imbracaminte sport', 'Incaltaminte sport', 'Echipamente sportive', 'Role si skateboard', 'Pescuit', 'Sporturi de iarna']],
                ['name' => 'Travel', 'children' => ['Trolere', 'Genti de voiaj', 'Accesorii voiaj']],
            ]],
            ['name' => 'Auto & Moto', 'children' => [
                ['name' => 'Anvelope si jante', 'children' => ['Anvelope vara', 'Anvelope iarna', 'Anvelope all season', 'Anvelope moto', 'Jante auto']],
                ['name' => 'Piese si consumabile auto', 'children' => ['Filtre auto', 'Placute si discuri frana', 'Baterii auto', 'Amortizoare', 'Stergatoare', 'Ulei motor', 'Aditivi auto', 'Antigel si lichide']],
                ['name' => 'Electronice auto', 'children' => ['Camere auto DVR', 'Multimedia auto', 'Difuzoare si subwoofere', 'Statii radio CB', 'Becuri auto']],
                ['name' => 'Accesorii auto', 'children' => ['Cutii portbagaj', 'Bare transversale', 'Huse scaune', 'Covorase auto', 'Frigidere auto', 'Suporturi bicicleta auto', 'Compresoare si redresoare', 'Truse si accesorii auto']],
                ['name' => 'Moto si vehicule electrice', 'children' => ['Casti moto', 'Manusi moto', 'Accesorii moto', 'ATV si UTV', 'Statii de incarcare vehicule electrice']],
            ]],
            ['name' => 'Jucarii, Copii & Bebe', 'children' => [
                ['name' => 'Bebelusi', 'children' => ['Scutece si servetele', 'Hrana bebe', 'Biberoane si accesorii hranire', 'Igiena si ingrijire bebe', 'Suzete si accesorii']],
                ['name' => 'Transport si camera copilului', 'children' => ['Carucioare', 'Scaune auto copii', 'Patuturi', 'Saltele copii', 'Mobilier camera copilului']],
                ['name' => 'Jucarii pe varste', 'children' => ['Jucarii 0-12 luni', 'Jucarii 1-3 ani', 'Jucarii 4-7 ani', 'Jucarii 8-11 ani', 'Jucarii 12+ ani']],
                ['name' => 'Jocuri si creativitate', 'children' => ['Jocuri educative', 'Puzzle', 'Seturi de constructie', 'Jocuri de societate', 'Jucarii creative', 'Papusi si plusuri', 'Masinute si vehicule']],
                ['name' => 'Exterior si activitati', 'children' => ['Trotinete copii', 'Triciclete', 'Piscine copii', 'Jucarii exterior']],
            ]],
        ];
    }

    private function presetMap(): array
    {
        return [
            'phone' => ['telefoane mobile', 'telefoane cu butoane'],
            'phone_accessories' => ['huse telefoane', 'folii si sticla protectie', 'incarcatoare telefoane', 'cabluri telefoane', 'power bank', 'suporturi auto telefon', 'selfie stick-uri'],
            'laptop' => ['laptopuri', 'laptopuri gaming', 'laptopuri business', 'laptopuri 2 in 1', 'macbook'],
            'laptop_accessories' => ['genti laptop', 'docking stations', 'standuri si coolere laptop', 'incarcatoare laptop', 'baterii laptop', 'tastaturi laptop', 'hard disk-uri notebook', 'memorii laptop'],
            'tablet' => ['tablete', 'tablete grafice', 'huse tablete', 'folie protectie tableta', 'tastaturi pentru tablete', 'stylus', 'docking si suporturi tablete'],
            'wearables' => ['smartwatch-uri', 'bratari fitness', 'casti true wireless', 'trackere gps'],
            'desktop' => ['desktop pc', 'desktop pc gaming', 'sisteme office', 'mini pc', 'all-in-one pc'],
            'monitor' => ['monitoare', 'monitoare gaming', 'accesorii monitoare'],
            'pc_components' => ['procesoare', 'placi de baza', 'placi video', 'memorii ram', 'ssd-uri interne', 'hard disk-uri interne', 'surse pc', 'carcase', 'coolere procesoare', 'coolere si ventilatoare carcasa', 'pasta termoconductoare', 'placi de sunet', 'placi de retea'],
            'pc_peripherals' => ['mouse', 'mouse gaming', 'tastaturi', 'tastaturi gaming', 'casti pc', 'microfoane pc', 'webcam-uri', 'boxe pc', 'mouse pad-uri', 'cititoare carduri', 'hub-uri usb', 'joystick-uri si controllere'],
            'network_storage' => ['hard disk-uri externe', 'ssd-uri externe', 'stick-uri usb', 'carduri de memorie', 'nas-uri', 'routere wireless', 'access point-uri', 'switch-uri', 'camere supraveghere ip', 'ups-uri'],
            'software' => ['sisteme de operare', 'office si productivitate', 'antivirus si securitate', 'software educatie'],
            'tv' => ['televizoare led', 'televizoare qled', 'televizoare oled', 'suporturi tv', 'media player-e'],
            'audio' => ['soundbar-uri', 'boxe portabile', 'boxe shelf si floorstanding', 'sisteme audio complete', 'amplificatoare si receivere', 'subwoofere', 'casti on-ear si over-ear', 'casti in-ear', 'microfoane', 'cabluri si adaptoare av'],
            'photo' => ['aparate foto mirrorless', 'aparate foto dslr', 'aparate foto compacte', 'aparate instant', 'camere video sport', 'drone', 'obiective', 'trepiede', 'gimbal-uri', 'blituri si lumini foto'],
            'projection' => ['videoproiectoare', 'ecrane proiectie', 'ebook readere'],
            'gaming' => ['console', 'jocuri consola', 'accesorii console', 'gamepad-uri', 'volane gaming', 'scaune gaming', 'ochelari vr', 'console gaming', 'accesorii gaming', 'jocuri pc', 'jocuri playstation', 'jocuri xbox', 'jocuri nintendo'],
            'appliances' => ['combine frigorifice', 'frigidere', 'side by side', 'lazi frigorifice', 'congelatoare', 'masini de spalat rufe', 'masini de spalat cu uscator', 'uscatoare de rufe', 'masini de spalat vase', 'aragazuri', 'cuptoare incorporabile', 'plite incorporabile', 'hote incorporabile', 'cuptoare cu microunde', 'espressoare', 'blendere', 'mixere', 'roboti de bucatarie', 'fierbatoare', 'toastere', 'air fryer', 'aspiratoare', 'aspiratoare verticale', 'aspiratoare robot', 'aparate de curatat cu abur', 'fiare de calcat', 'statii de calcat', 'aparate de aer conditionat', 'purificatoare de aer', 'dezumidificatoare', 'umidificatoare', 'ventilatoare si racitoare de aer', 'aeroterme', 'calorifere electrice'],
            'office' => ['imprimante laser', 'imprimante inkjet', 'cartuse si tonere', 'distrugatoare documente', 'laminatoare', 'hartie copiator', 'caiete', 'pixuri si stilouri', 'markere si evidentiatoare', 'ghiozdane si genti', 'penare'],
            'fashion' => ['rochii', 'bluze femei', 'tricouri femei', 'camasi femei', 'pantaloni femei', 'jeansi femei', 'fuste', 'hanorace femei', 'geci femei', 'lenjerie femei', 'costume de baie femei', 'tricouri barbati', 'camasi barbati', 'hanorace barbati', 'pantaloni barbati', 'jeansi barbati', 'sacouri', 'geci barbati', 'lenjerie barbati', 'haine copii', 'treninguri'],
            'footwear' => ['incaltaminte femei', 'incaltaminte barbati', 'incaltaminte copii', 'pantofi sport'],
            'fashion_accessories' => ['genti femei', 'bijuterii femei', 'portofele si curele', 'ceasuri barbati', 'accesorii copii', 'ghiozdane copii', 'rucsacuri sport', 'ochelari de soare', 'trolere', 'genti de voiaj', 'accesorii voiaj'],
            'beauty' => ['demachiere si curatare', 'creme fata', 'seruri', 'masti ten', 'spf', 'geluri de dus', 'lotiuni si creme corp', 'deodorante', 'fond de ten', 'rujuri', 'mascara', 'farduri si palete', 'parfumuri femei', 'parfumuri barbati', 'seturi cadou', 'sampon', 'balsam', 'masti de par', 'vopsea de par', 'uscatoare de par', 'placi de par', 'ondulatoare', 'perii electrice', 'pasta de dinti', 'periute de dinti', 'periute electrice', 'aparate de ras', 'aparate de tuns', 'epilatoare', 'vitamine si suplimente', 'articole wellness', 'aparate si dispozitive medicale'],
            'home' => ['mobila living', 'mobila dormitor', 'mobila bucatarie', 'mobila birou', 'mobila hol', 'saltele', 'perne si pilote', 'lenjerii de pat', 'covoare', 'perdele si draperii', 'corpuri de iluminat', 'decoratiuni interioare', 'vase pentru gatit', 'seturi de farfurii', 'tacamuri', 'cutii depozitare', 'accesorii servire', 'mobilier de gradina', 'gratare si accesorii', 'unelte de gradinarit', 'aparate de spalat cu presiune', 'ghivece si suporturi', 'plante si seminte', 'bormasini si rotopercutoare', 'polizoare si slefuitoare', 'surubelnite electrice', 'truse de scule', 'generatoare', 'vopsea si tencuieli', 'materiale de constructii', 'baterii sanitare', 'mobilier baie', 'cabine de dus si coloane', 'boilere', 'radiatoare', 'centrale termice'],
            'sport' => ['benzi de alergat', 'biciclete fitness', 'gantere si seturi', 'banci fitness', 'saltele fitness', 'nutritie sportiva', 'corturi camping', 'sac de dormit', 'mobilier camping', 'arzatoare outdoor', 'rucsacuri drumetie', 'lanterne', 'cutite si bricege', 'biciclete copii', 'biciclete oras', 'biciclete electrice', 'accesorii biciclete', 'trotinete', 'trotinete electrice', 'hoverboard-uri', 'imbracaminte sport', 'incaltaminte sport', 'echipamente sportive', 'role si skateboard', 'pescuit', 'sporturi de iarna'],
            'tires' => ['anvelope vara', 'anvelope iarna', 'anvelope all season', 'anvelope moto'],
            'wheels' => ['jante auto'],
            'auto_parts' => ['filtre auto', 'placute si discuri frana', 'baterii auto', 'amortizoare', 'stergatoare', 'ulei motor', 'aditivi auto', 'antigel si lichide'],
            'auto_electronics' => ['camere auto dvr', 'multimedia auto', 'difuzoare si subwoofere', 'statii radio cb', 'becuri auto'],
            'auto_accessories' => ['cutii portbagaj', 'bare transversale', 'huse scaune', 'covorase auto', 'frigidere auto', 'suporturi bicicleta auto', 'compresoare si redresoare', 'truse si accesorii auto'],
            'kids' => ['scutece si servetele', 'hrana bebe', 'biberoane si accesorii hranire', 'igiena si ingrijire bebe', 'suzete si accesorii', 'carucioare', 'scaune auto copii', 'patuturi', 'saltele copii', 'mobilier camera copilului', 'jucarii 0-12 luni', 'jucarii 1-3 ani', 'jucarii 4-7 ani', 'jucarii 8-11 ani', 'jucarii 12+ ani', 'jocuri educative', 'puzzle', 'seturi de constructie', 'jocuri de societate', 'jucarii creative', 'papusi si plusuri', 'masinute si vehicule', 'trotinete copii', 'triciclete', 'piscine copii', 'jucarii exterior'],
        ];
    }
    private function presetAttributes(): array
    {
        return [
            'generic' => [$this->a('Brand','brand','text',['f'=>true,'s'=>1]), $this->a('Model','model','text',['f'=>true,'s'=>2]), $this->a('Culoare','color','select',['f'=>true,'v'=>true,'s'=>10,'o'=>['Negru','Alb','Gri','Albastru','Rosu','Verde','Roz','Bej','Maro']])],
            'phone' => [$this->a('Brand','brand','select',['r'=>true,'f'=>true,'s'=>1,'o'=>['Apple','Samsung','Xiaomi','Honor','Huawei','Motorola','Google','Nothing']]), $this->a('Model','model','text',['r'=>true,'f'=>true,'s'=>2]), $this->a('Culoare','color','select',['f'=>true,'v'=>true,'s'=>3,'o'=>['Negru','Alb','Albastru','Titan','Mov','Roz','Verde','Argintiu']]), $this->a('Capacitate stocare','storage','select',['r'=>true,'f'=>true,'v'=>true,'s'=>4,'o'=>['32 GB','64 GB','128 GB','256 GB','512 GB','1 TB']]), $this->a('RAM','ram','select',['f'=>true,'s'=>5,'o'=>['3 GB','4 GB','6 GB','8 GB','12 GB','16 GB']]), $this->a('Diagonal ecran','screen_size','number',['f'=>true,'s'=>6,'um'=>'fixed','du'=>'inch']), $this->a('Retea','network','select',['f'=>true,'s'=>7,'o'=>['4G','5G']]), $this->a('Capacitate baterie','battery_capacity','number',['f'=>true,'s'=>8,'um'=>'fixed','du'=>'mAh']), $this->a('Sistem operare','os','select',['f'=>true,'s'=>9,'o'=>['Android','iOS','KaiOS']])],
            'phone_accessories' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Apple','Samsung','Baseus','UGREEN','Anker','Belkin','Spigen']]), $this->a('Compatibilitate','compatibility','text',['f'=>true,'s'=>2]), $this->a('Tip accesoriu','accessory_type','select',['f'=>true,'s'=>3,'o'=>['Husa','Folie','Sticla protectie','Incarcator','Cablu','Power bank','Suport auto']]), $this->a('Conector','connector','select',['f'=>true,'s'=>4,'o'=>['USB-C','Lightning','Micro USB','MagSafe','Wireless']]), $this->a('Putere incarcare','power_output','number',['f'=>true,'s'=>5,'um'=>'fixed','du'=>'W'])],
            'laptop' => [$this->a('Brand','brand','select',['r'=>true,'f'=>true,'s'=>1,'o'=>['Apple','Lenovo','HP','Dell','ASUS','Acer','MSI','Huawei']]), $this->a('Model','model','text',['r'=>true,'f'=>true,'s'=>2]), $this->a('Procesor','cpu','text',['f'=>true,'s'=>3]), $this->a('RAM','ram','select',['f'=>true,'s'=>4,'o'=>['8 GB','16 GB','24 GB','32 GB','64 GB']]), $this->a('Capacitate stocare','storage','select',['f'=>true,'s'=>5,'o'=>['256 GB','512 GB','1 TB','2 TB']]), $this->a('Diagonala display','screen_size','select',['f'=>true,'s'=>6,'o'=>['13.3"','14"','15.6"','16"','17.3"']]), $this->a('Rezolutie','resolution','select',['f'=>true,'s'=>7,'o'=>['HD','Full HD','2.5K','3K','4K']]), $this->a('Refresh rate','refresh_rate','select',['f'=>true,'s'=>8,'o'=>['60 Hz','90 Hz','120 Hz','144 Hz','165 Hz','240 Hz']]), $this->a('Placa video','gpu','text',['f'=>true,'s'=>9]), $this->a('Sistem operare','os','select',['f'=>true,'s'=>10,'o'=>['Windows 11','macOS','Linux','Fara OS']])],
            'laptop_accessories' => [$this->a('Tip accesoriu','accessory_type','select',['f'=>true,'s'=>1,'o'=>['Geanta','Docking station','Cooler','Incarcator','Baterie','Tastatura','Memorie','Hard disk']]), $this->a('Compatibilitate laptop','compatibility','text',['f'=>true,'s'=>2]), $this->a('Dimensiune compatibila','compatible_size','text',['f'=>true,'s'=>3]), $this->a('Conectivitate','connectivity','text',['f'=>true,'s'=>4])],
            'tablet' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Apple','Samsung','Lenovo','Xiaomi','Huawei','Wacom','Huion']]), $this->a('Diagonala','screen_size','number',['f'=>true,'s'=>2,'um'=>'fixed','du'=>'inch']), $this->a('Capacitate stocare','storage','select',['f'=>true,'v'=>true,'s'=>3,'o'=>['32 GB','64 GB','128 GB','256 GB','512 GB','1 TB']]), $this->a('RAM','ram','select',['f'=>true,'s'=>4,'o'=>['3 GB','4 GB','6 GB','8 GB','12 GB','16 GB']]), $this->a('Conectivitate','connectivity','select',['f'=>true,'s'=>5,'o'=>['Wi-Fi','Wi-Fi + Cellular','Bluetooth']])],
            'wearables' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Apple','Samsung','Huawei','Garmin','Xiaomi','Amazfit','Sony','JBL']]), $this->a('Compatibilitate','compatibility','select',['f'=>true,'s'=>2,'o'=>['Android','iOS','Android + iOS']]), $this->a('Conectivitate','connectivity','multiselect',['f'=>true,'s'=>3,'o'=>['Bluetooth','Wi-Fi','GPS','NFC','LTE']]), $this->a('Autonomie','battery_life','text',['f'=>true,'s'=>4])],
            'desktop' => [$this->a('Procesor','cpu','text',['f'=>true,'s'=>1]), $this->a('RAM','ram','select',['f'=>true,'s'=>2,'o'=>['8 GB','16 GB','32 GB','64 GB','128 GB']]), $this->a('Stocare','storage','text',['f'=>true,'s'=>3]), $this->a('Placa video','gpu','text',['f'=>true,'s'=>4]), $this->a('Sistem operare','os','select',['f'=>true,'s'=>5,'o'=>['Windows 11','Linux','Fara OS']]), $this->a('Factor forma','form_factor','select',['f'=>true,'s'=>6,'o'=>['Tower','Mini Tower','SFF','Mini PC','All-in-One']])],
            'monitor' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Dell','LG','Samsung','ASUS','AOC','MSI','BenQ','Philips']]), $this->a('Diagonala','screen_size','number',['f'=>true,'s'=>2,'um'=>'fixed','du'=>'inch']), $this->a('Rezolutie','resolution','select',['f'=>true,'s'=>3,'o'=>['Full HD','QHD','4K','Ultrawide QHD']]), $this->a('Tip panou','panel_type','select',['f'=>true,'s'=>4,'o'=>['IPS','VA','TN','OLED']]), $this->a('Refresh rate','refresh_rate','select',['f'=>true,'s'=>5,'o'=>['60 Hz','75 Hz','100 Hz','120 Hz','144 Hz','165 Hz','240 Hz']])],
            'pc_components' => [$this->a('Socket / platforma','socket','text',['f'=>true,'s'=>1]), $this->a('Capacitate','capacity','text',['f'=>true,'s'=>2]), $this->a('Interfata','interface','text',['f'=>true,'s'=>3]), $this->a('Form factor','form_factor','text',['f'=>true,'s'=>4]), $this->a('Frecventa','frequency','text',['f'=>true,'s'=>5]), $this->a('Putere','power','number',['f'=>true,'s'=>6,'um'=>'fixed','du'=>'W']), $this->a('Iluminare RGB','rgb','boolean',['f'=>true,'s'=>7])],
            'pc_peripherals' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Logitech','Razer','SteelSeries','HyperX','Corsair','ASUS','A4Tech']]), $this->a('Conectivitate','connectivity','multiselect',['f'=>true,'s'=>2,'o'=>['USB','USB-C','Bluetooth','Wireless 2.4 GHz','Jack 3.5 mm']]), $this->a('Tip utilizare','usage_type','select',['f'=>true,'s'=>3,'o'=>['Office','Gaming','Streaming','Portable']]), $this->a('Iluminare','lighting','select',['f'=>true,'s'=>4,'o'=>['Fara iluminare','RGB','Monocrom']])],
            'network_storage' => [$this->a('Capacitate','capacity','select',['f'=>true,'s'=>1,'o'=>['32 GB','64 GB','128 GB','256 GB','512 GB','1 TB','2 TB','4 TB','8 TB']]), $this->a('Interfata / standard','interface_standard','text',['f'=>true,'s'=>2]), $this->a('Conectivitate','connectivity','text',['f'=>true,'s'=>3]), $this->a('Viteza maxima','max_speed','text',['f'=>true,'s'=>4])],
            'software' => [$this->a('Tip licenta','license_type','select',['f'=>true,'s'=>1,'o'=>['Retail','OEM','Subscription','Education']]), $this->a('Durata','license_duration','select',['f'=>true,'s'=>2,'o'=>['Perpetua','1 an','2 ani','3 ani']]), $this->a('Numar dispozitive','device_count','select',['f'=>true,'s'=>3,'o'=>['1','3','5','10']]), $this->a('Livrare','delivery_type','select',['f'=>true,'s'=>4,'o'=>['Cheie electronica','Card licenta','Download digital']])],
            'tv' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Samsung','LG','Sony','Philips','TCL','Hisense']]), $this->a('Diagonala','screen_size','select',['f'=>true,'s'=>2,'o'=>['32"','43"','50"','55"','65"','75"','85"']]), $this->a('Rezolutie','resolution','select',['f'=>true,'s'=>3,'o'=>['HD','Full HD','4K','8K']]), $this->a('Tehnologie display','display_technology','select',['f'=>true,'s'=>4,'o'=>['LED','QLED','OLED','Mini LED']]), $this->a('Smart TV','smart_tv','boolean',['f'=>true,'s'=>5])],
            'audio' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Sony','JBL','Bose','Samsung','LG','Sennheiser','Marshall','Apple']]), $this->a('Conectivitate','connectivity','multiselect',['f'=>true,'s'=>2,'o'=>['Bluetooth','Wi-Fi','USB-C','Jack 3.5 mm','HDMI ARC','Optic']]), $this->a('Putere RMS','rms_power','number',['f'=>true,'s'=>3,'um'=>'fixed','du'=>'W']), $this->a('Autonomie','battery_life','text',['f'=>true,'s'=>4])],
            'photo' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Canon','Nikon','Sony','Fujifilm','DJI','GoPro','Panasonic']]), $this->a('Tip senzor','sensor_type','select',['f'=>true,'s'=>2,'o'=>['Full Frame','APS-C','Micro Four Thirds','1 inch']]), $this->a('Rezolutie foto','resolution_mp','number',['f'=>true,'s'=>3,'um'=>'fixed','du'=>'MP']), $this->a('Rezolutie video','video_resolution','select',['f'=>true,'s'=>4,'o'=>['Full HD','4K','5.3K','6K','8K']]), $this->a('Stabilizare','stabilization','boolean',['f'=>true,'s'=>5])],
            'projection' => [$this->a('Rezolutie nativa','native_resolution','text',['f'=>true,'s'=>1]), $this->a('Luminozitate','brightness','number',['f'=>true,'s'=>2,'um'=>'fixed','du'=>'ANSI lm']), $this->a('Conectivitate','connectivity','text',['f'=>true,'s'=>3])],
            'gaming' => [$this->a('Platforma','platform','select',['f'=>true,'s'=>1,'o'=>['PC','PlayStation 5','PlayStation 4','Xbox Series','Xbox One','Nintendo Switch','Universal']]), $this->a('Tip produs','product_type','select',['f'=>true,'s'=>2,'o'=>['Consola','Joc','Controller','Accesoriu','Volan','Scaun','VR']]), $this->a('Conectivitate','connectivity','multiselect',['f'=>true,'s'=>3,'o'=>['USB','USB-C','Bluetooth','Wireless 2.4 GHz','Jack 3.5 mm']]), $this->a('PEGI / varsta','age_rating','select',['f'=>true,'s'=>4,'o'=>['3+','7+','12+','16+','18+']])],
            'appliances' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Samsung','LG','Bosch','Whirlpool','Beko','Gorenje','Philips','Tefal','Dyson']]), $this->a('Tip montare','installation_type','select',['f'=>true,'s'=>2,'o'=>['Standard','Incorporabil']]), $this->a('Capacitate','capacity','number',['f'=>true,'s'=>3,'um'=>'free','au'=>['L','kg','seturi']]), $this->a('Putere','power','number',['f'=>true,'s'=>4,'um'=>'fixed','du'=>'W']), $this->a('Clasa energetica','energy_class','select',['f'=>true,'s'=>5,'o'=>['A','B','C','D','E','F','G']])],
            'office' => [$this->a('Brand','brand','text',['f'=>true,'s'=>1]), $this->a('Format','format','select',['f'=>true,'s'=>2,'o'=>['A3','A4','A5','B5','Universal']]), $this->a('Culoare','color','select',['f'=>true,'v'=>true,'s'=>3,'o'=>['Negru','Albastru','Rosu','Verde','Roz','Multicolor']]), $this->a('Set / bucati','pack_size','number',['f'=>true,'s'=>4])],
            'fashion' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Nike','Adidas','Puma','Zara','H&M','Reserved','Mango','Tom Tailor','LC Waikiki']]), $this->a('Marime','size','select',['r'=>true,'f'=>true,'v'=>true,'s'=>2,'o'=>['XXS','XS','S','M','L','XL','XXL','3XL']]), $this->a('Culoare','color','select',['r'=>true,'f'=>true,'v'=>true,'s'=>3,'o'=>['Negru','Alb','Gri','Bleumarin','Albastru','Rosu','Verde','Roz','Bej','Maro']]), $this->a('Material','material','text',['f'=>true,'s'=>4]), $this->a('Sezon','season','select',['f'=>true,'s'=>5,'o'=>['Primavara','Vara','Toamna','Iarna','All season']]), $this->a('Croiala','fit','select',['f'=>true,'s'=>6,'o'=>['Slim fit','Regular fit','Relaxed fit','Oversized']])],
            'footwear' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Nike','Adidas','Puma','Skechers','Geox','ECCO','New Balance']]), $this->a('Marime','size','select',['r'=>true,'f'=>true,'v'=>true,'s'=>2,'o'=>['28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46']]), $this->a('Culoare','color','select',['f'=>true,'v'=>true,'s'=>3,'o'=>['Negru','Alb','Gri','Bej','Maro','Bleumarin','Rosu','Roz']]), $this->a('Material exterior','outer_material','text',['f'=>true,'s'=>4]), $this->a('Sezon','season','select',['f'=>true,'s'=>5,'o'=>['Primavara','Vara','Toamna','Iarna','All season']])],
            'fashion_accessories' => [$this->a('Material','material','text',['f'=>true,'s'=>1]), $this->a('Culoare','color','select',['f'=>true,'v'=>true,'s'=>2,'o'=>['Negru','Alb','Gri','Bej','Maro','Albastru','Rosu','Roz','Auriu','Argintiu']]), $this->a('Tip accesoriu','accessory_type','text',['f'=>true,'s'=>3])],
            'beauty' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['L Oreal','Maybelline','Nivea','CeraVe','La Roche-Posay','Philips','Remington','Oral-B']]), $this->a('Tip produs','product_type','text',['f'=>true,'s'=>2]), $this->a('Volum / cantitate','volume','number',['f'=>true,'s'=>3,'um'=>'free','au'=>['ml','g','capsule','buc']]), $this->a('Nuanta','shade','text',['f'=>true,'v'=>true,'s'=>4]), $this->a('Beneficiu principal','main_benefit','text',['f'=>true,'s'=>5])],
            'home' => [$this->a('Material','material','text',['f'=>true,'s'=>1]), $this->a('Dimensiuni','dimensions','text',['f'=>true,'s'=>2]), $this->a('Culoare','color','select',['f'=>true,'v'=>true,'s'=>3,'o'=>['Alb','Negru','Gri','Maro','Bej','Antracit','Stejar','Wenge']]), $this->a('Tip montaj','assembly_type','select',['f'=>true,'s'=>4,'o'=>['Asamblat','Necesita montaj']])],
            'sport' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Nike','Adidas','Puma','Salomon','Columbia','Under Armour','Pegas','FitTronic']]), $this->a('Tip activitate','activity_type','text',['f'=>true,'s'=>2]), $this->a('Nivel utilizare','usage_level','select',['f'=>true,'s'=>3,'o'=>['Incepator','Intermediar','Avansat','Profesional']]), $this->a('Marime','size','text',['f'=>true,'v'=>true,'s'=>4])],
            'tires' => [$this->a('Brand','brand','select',['r'=>true,'f'=>true,'s'=>1,'o'=>['Michelin','Continental','Pirelli','Goodyear','Bridgestone','Hankook','Nokian','Kumho','Toyo','Yokohama']]), $this->a('Latime','width','select',['r'=>true,'f'=>true,'v'=>true,'s'=>2,'o'=>['145','155','165','175','185','195','205','215','225','235','245','255','265','275','285','295']]), $this->a('Profil','profile','select',['r'=>true,'f'=>true,'v'=>true,'s'=>3,'o'=>['25','30','35','40','45','50','55','60','65','70','75','80']]), $this->a('Diametru','rim','select',['r'=>true,'f'=>true,'v'=>true,'s'=>4,'o'=>['R12','R13','R14','R15','R16','R17','R18','R19','R20','R21','R22']]), $this->a('Indice sarcina','load_index','text',['f'=>true,'s'=>5]), $this->a('Indice viteza','speed_index','text',['f'=>true,'s'=>6]), $this->a('Runflat','runflat','boolean',['f'=>true,'s'=>7])],
            'wheels' => [$this->a('Diametru','diameter','select',['f'=>true,'v'=>true,'s'=>1,'o'=>['13"','14"','15"','16"','17"','18"','19"','20"','21"','22"']]), $this->a('Latime janta','wheel_width','select',['f'=>true,'s'=>2,'o'=>['5J','5.5J','6J','6.5J','7J','7.5J','8J','8.5J','9J','9.5J','10J']]), $this->a('Numar prezoane','bolt_count','select',['f'=>true,'s'=>3,'o'=>['4','5','6']]), $this->a('PCD','pcd','select',['f'=>true,'s'=>4,'o'=>['4x100','4x108','5x100','5x108','5x110','5x112','5x114.3','5x120','5x130','6x139.7']]), $this->a('ET','offset','text',['f'=>true,'s'=>5])],
            'auto_parts' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['Bosch','Mann','Mahle','Brembo','ATE','Valeo','Castrol','Mobil']]), $this->a('Compatibilitate auto','compatibility','text',['f'=>true,'s'=>2]), $this->a('Cod OEM','oem_code','text',['f'=>true,'s'=>3]), $this->a('Cantitate','volume','number',['f'=>true,'s'=>4,'um'=>'free','au'=>['L','ml','buc']])],
            'auto_electronics' => [$this->a('Compatibilitate auto','compatibility','text',['f'=>true,'s'=>1]), $this->a('Conectivitate','connectivity','multiselect',['f'=>true,'s'=>2,'o'=>['Bluetooth','Wi-Fi','USB','CarPlay','Android Auto']]), $this->a('Dimensiune ecran','screen_size','number',['f'=>true,'s'=>3,'um'=>'fixed','du'=>'inch'])],
            'auto_accessories' => [$this->a('Compatibilitate auto','compatibility','text',['f'=>true,'s'=>1]), $this->a('Material','material','text',['f'=>true,'s'=>2]), $this->a('Culoare','color','select',['f'=>true,'v'=>true,'s'=>3,'o'=>['Negru','Gri','Bej','Rosu']])],
            'kids' => [$this->a('Brand','brand','select',['f'=>true,'s'=>1,'o'=>['LEGO','Hasbro','Mattel','Fisher-Price','VTech','Philips Avent','Huggies','Pampers']]), $this->a('Varsta recomandata','age_group','select',['f'=>true,'s'=>2,'o'=>['0-6 luni','6-12 luni','1-3 ani','4-7 ani','8-11 ani','12+ ani']]), $this->a('Gen recomandat','recommended_gender','select',['f'=>true,'s'=>3,'o'=>['Fete','Baieti','Unisex']]), $this->a('Material','material','text',['f'=>true,'s'=>4]), $this->a('Piese / bucati','piece_count','number',['f'=>true,'s'=>5])],
        ];
    }

    private function children(Category $parent, array $children): void
    {
        foreach ($children as $i => $child) {
            if (is_string($child)) {
                $leaf = $this->cat($child, $parent->id, $i);
                $this->seedLeaf($leaf);
                continue;
            }

            $node = $this->cat($child['name'], $parent->id, $i);
            $this->children($node, $child['children'] ?? []);
        }
    }

    private function seedLeaf(Category $category): void
    {
        $path = $this->path($category);
        $presets = $this->presetMap();
        $attributes = $this->presetAttributes();
        $key = 'generic';

        foreach ($presets as $preset => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($path, Str::lower($needle))) {
                    $key = $preset;
                    break 2;
                }
            }
        }

        foreach ($attributes['generic'] as $attribute) {
            $this->attr($category, $attribute);
        }

        if ($key !== 'generic') {
            foreach ($attributes[$key] as $attribute) {
                $this->attr($category, $attribute);
            }
        }
    }
    private function cat(string $name, ?int $parentId, int $sort): Category
    {
        $parent = $parentId ? Category::find($parentId) : null;
        $slug = $parent ? $parent->slug . '-' . Str::slug($name) : Str::slug($name);

        return Category::updateOrCreate(
            ['parent_id' => $parentId, 'name' => $name],
            ['slug' => $slug, 'is_active' => true, 'sort_order' => $sort]
        );
    }

    private function attr(Category $category, array $data): void
    {
        $attribute = CategoryAttribute::updateOrCreate(
            ['category_id' => $category->id, 'slug' => $data['slug']],
            [
                'name' => $data['name'],
                'type' => $data['type'],
                'unit_mode' => $data['unit_mode'] ?? 'none',
                'default_unit' => $data['default_unit'] ?? null,
                'allowed_units' => $data['allowed_units'] ?? null,
                'is_required' => $data['is_required'] ?? false,
                'is_filterable' => $data['is_filterable'] ?? false,
                'is_variant' => $data['is_variant'] ?? false,
                'sort_order' => $data['sort_order'] ?? 0,
            ]
        );

        if (!in_array($data['type'], ['select', 'multiselect'], true)) {
            return;
        }

        foreach ($data['options'] ?? [] as $i => $option) {
            CategoryAttributeOption::updateOrCreate(
                ['category_attribute_id' => $attribute->id, 'value' => Str::slug($option)],
                ['label' => $option, 'sort_order' => $i]
            );
        }
    }

    private function a(string $name, string $slug, string $type, array $x = []): array
    {
        return [
            'name' => $name,
            'slug' => $slug,
            'type' => $type,
            'is_required' => $x['r'] ?? false,
            'is_filterable' => $x['f'] ?? false,
            'is_variant' => $x['v'] ?? false,
            'sort_order' => $x['s'] ?? 0,
            'options' => $x['o'] ?? [],
            'unit_mode' => $x['um'] ?? 'none',
            'default_unit' => $x['du'] ?? null,
            'allowed_units' => $x['au'] ?? null,
        ];
    }

    private function path(Category $category): string
    {
        $parts = [];
        $node = $category->loadMissing('parent');

        while ($node) {
            $parts[] = Str::lower($node->name);
            $node = $node->parent;
        }

        return implode(' > ', array_reverse($parts));
    }
}
