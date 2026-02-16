<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6 sm:p-10">
                <h1 class="text-3xl font-extrabold text-gray-900">Termeni și condiții</h1>
                <p class="mt-3 text-sm text-gray-500">
                    Ultima actualizare: <?php echo e(date('d.m.Y')); ?>

                </p>

                <div class="prose max-w-none mt-6">

                    <h2>1. Dispoziții generale</h2>
                    <p>
                        Acest site este deținut de <strong>COMPANIA TA SRL</strong>.
                        La plasarea unei comenzi în magazinul online, Cumpărătorul acceptă
                        Termenii și Condițiile de vânzare a produselor și/sau prestare a serviciilor,
                        elaborate în conformitate cu legislația Republicii Moldova.
                    </p>
                    <p>
                        Utilizarea site-ului presupune acceptarea Termenilor și Condițiilor,
                        în conformitate cu Legea nr. 284/2004 privind Comerțul Electronic și
                        Legea nr. 105/2003 privind protecția drepturilor consumatorului.
                    </p>
                    <p>
                        Vânzătorul își rezervă dreptul de a modifica acești Termeni și Condiții,
                        iar Cumpărătorul este obligat să urmărească modificările afișate pe site.
                    </p>

                    <h2>2. Protecția datelor cu caracter personal</h2>
                    <p>
                        Prin utilizarea site-ului, sunteți de acord cu colectarea și prelucrarea
                        datelor cu caracter personal necesare procesării, confirmării și livrării comenzilor.
                    </p>
                    <p>
                        Datele personale sunt prelucrate exclusiv în scopuri legitime:
                        procesarea comenzilor, livrare, comunicare cu clientul, promoții,
                        cookies, Google Analytics și newsletter (dacă este cazul).
                    </p>
                    <p>
                        Prelucrarea datelor se face în conformitate cu Legea nr. 133/2011
                        privind protecția datelor cu caracter personal.
                    </p>
                    <p>
                        Transmiterea datelor prin internet nu poate fi garantată ca fiind
                        complet sigură, iar utilizatorul își asumă această responsabilitate.
                    </p>

                    <h2>3. Înregistrarea și achitarea comenzii</h2>
                    <p>
                        Plata comenzilor se efectuează online, cu cardul bancar.
                        După efectuarea plății, clientul primește confirmare prin email
                        și bon fiscal aferent tranzacției.
                    </p>
                    <p>
                        Plățile online sunt procesate securizat prin sistemul băncii
                        <strong>MAIB</strong>, utilizând standardul internațional de securitate
                        <strong>3D Secure</strong>, care presupune autentificarea fiecărei tranzacții
                        prin cod unic.
                    </p>
                    <p>
                        Rambursarea mijloacelor bănești se efectuează exclusiv pe cardul
                        utilizat la efectuarea plății.
                    </p>
                    <p>
                        Pentru efectuarea plății, utilizatorului i se pot solicita următoarele date:
                    </p>
                    <ul>
                        <li>Numărul cardului (16 cifre)</li>
                        <li>Data expirării (lună/an)</li>
                        <li>Codul CVC/CVV</li>
                        <li>Numele și prenumele de pe card</li>
                    </ul>
                    <p>
                        În cazul în care valuta cardului diferă de MDL, conversia sumei
                        se va efectua conform condițiilor băncii emitente.
                    </p>

                    <h2>4. Livrarea produselor</h2>
                    <p>
                        După confirmarea comenzii, clientului i se comunică termenul estimativ
                        de livrare.
                    </p>
                    <p>
                        Livrarea se efectuează pe teritoriul Republicii Moldova.
                        Termenele pot varia în funcție de localitate, disponibilitatea produselor
                        sau situații excepționale (condiții meteo, sărbători legale etc.).
                    </p>
                    <p>
                        În zilele de duminică și sărbători legale nu se efectuează livrări.
                    </p>
                    <p>
                        La primirea produselor, clientul este obligat să verifice integritatea
                        ambalajului și prezența bonului fiscal în prezența curierului.
                    </p>

                    <h2>5. Dreptul la retur</h2>
                    <p>
                        Conform legislației în vigoare, consumatorul are dreptul să returneze
                        produsele în termen de <strong>14 zile</strong> de la data achitării.
                    </p>
                    <p>
                        Rambursarea sumei achitate se va efectua pe același card utilizat la plată,
                        după acceptarea cererii de retur.
                    </p>
                    <p>
                        Produsele care nu pot fi returnate sunt cele prevăzute în Anexa nr. 2
                        a Hotărârii Guvernului nr. 1465/2003.
                    </p>

                    <h2>6. Politica de confidențialitate</h2>
                    <p>
                        Furnizorul nu colectează și nu stochează datele cardurilor bancare.
                        Acestea sunt procesate exclusiv de sistemele securizate ale băncii.
                    </p>
                    <p>
                        Datele personale nu vor fi divulgate terților, cu excepția cazurilor
                        prevăzute de legislația în vigoare sau partenerilor implicați în procesul
                        de plată și livrare.
                    </p>

                    <h2>7. Date de contact</h2>
                    <p>
                        Denumire: <strong>COMPANIA TA SRL</strong><br>
                        IDNO: <strong>XXXXXXXX</strong><br>
                        Adresa juridică: <strong>Chișinău, Republica Moldova</strong><br>
                        Telefon: <strong>+373 XX XXX XXX</strong><br>
                        Email: <strong>support@site.md</strong>
                    </p>

                </div>

            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/shop/terms.blade.php ENDPATH**/ ?>