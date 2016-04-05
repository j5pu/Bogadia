<?php
/* Template Name: B-shop Home */
get_header();
function is_mobile() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    return strpos($userAgent, 'mobile');
}
?>

<div class="row bshop-header">
    <div class="col-md-12">
        <a href="/por-que-bogadia">
        <?php
        if(!is_mobile()){
        ?>
            <img class="img-responsive b-shop-banner" src="/wp-content/uploads/2015/08/redes-tienda2grandealargado.jpg">
        <?php
        } else {
        ?>
            <img id="b-shop-banner-movil" class="img-responsive b-shop-banner b-shop-logo" src="/wp-content/uploads/2015/08/redes-tiendamovildefinitivo.jpg">
            <img id="b-shop-banner-tablet" class="img-responsive b-shop-banner b-shop-logo " src="/wp-content/uploads/2015/08/redes-tienda2grandealargadotablet.jpg" style="display: none;">
        <?php
        }
        ?>
        </div>
        </a>
</div>
<div class="row">
<!--            phetnia-->
    <div id="phetnia" class="col-sm-6 col-md-6 b-shop-designer">
        <a href="/phetnia">
            <img class="img-responsive" src="/wp-content/uploads/2015/08/phetnia2-cuadrada.jpg">
        </a>

        <?php echo do_shortcode('[product_attribute attribute="coleccion" filter="phetnia" per_page="6" columns="2"]'); ?>

        <div class="row b-shop-designers">
            <div class="col-xs-8 col-sm-8 col-md-8 b-shop-designers">
                <a href="/lucrecia">
                    <img class="img-responsive" src="/wp-content/uploads/2015/08/lucrecia-cuadrada.jpg">
                </a>
            </div>

            <div class="col-xs-4 col-sm-4 col-md-4 text-center b-shop-designers">
                <p> </p>
                <a href="/lucrecia">
                    <h3>Lucrecia PQ</h3>
                </a>
                <small>El arte y la moda de Málaga.</small>
                    <a href="/lucrecia">
                        <i>Leer bio</i>
                </a>
            </div>
        </div>
    </div>
<!--            Neon-->
    <div id="neon" class="col-sm-6 col-md-6 b-shop-designer">
        <a href="/manifiesto-neon">
            <img class="img-responsive" src="/wp-content/uploads/2015/08/neon2-cuadrada.jpg">
        </a>

        <?php echo do_shortcode('[product_attribute attribute="coleccion" filter="neon" per_page="6" columns="2"]'); ?>

        <div class="row b-shop-designers">
            <div class="col-xs-8 col-sm-8 col-md-8">
                <a href="/maria-cidfuentes-2">
                    <img class="img-responsive" src="/wp-content/uploads/2015/07/maria-foto-bio.jpg">
                </a>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 text-center">
                <p> </p>
                <a href="/maria-cidfuentes-2/">
                    <h3>María Cidfuentes</h3>
                </a>
                <small>Periodista, pero diseñadora de profesión.</small>
                    <a href="/maria-cidfuentes-2">
                        <i>Leer bio</i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
