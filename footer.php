<!--contact us section-->
<section class="contacts" id="contacts">
    <h1 class="heading"><span>contact us</span></h1>
   
     <div class="rows">
    <div class="images">
        <img src="perso_logo/homme _merci.png" alt="">
    </div>
    <form action="">
        <input type="text" placeholder="name" class="boxs">
        
        <input type="email" placeholder="email" class="boxs">
        
    <textarea name="message" class="boxs" placeholder="laissez un message juste ici" id="messages" cols="30" rows="10" required></textarea>
    
         <input type="submit" value="send message" class="btns">
    </form>
    
</div>

</section>

<section class="footer">
    <div class="box-container">
        <div class="box">
            <h3>quick links</h3>
                <a href="test.php">Home</a>
                <a href="test.php#aboutus">About us</a>
                <a href="test.php#products">Favoris</a>
            <a href="test.php#Boites&couvertures">Boites&couvertures</a>
            <a href="test.php#produits">Produits</a>
            <a href="test.php#contact">Contact us</a>
    
        </div>
        <div class="box">
            <h3>extras links</h3>
            <a href="moncompte.php">mon compte</a>
            <a href="mon_panier.php">mon panier</a>
            <a href="test.php#Boites&couvertures">passer commande</a>
           
        </div>
        <div class="box">
            <h3>categories</h3>
            <a href="macaron_rouge.php">fruits rouge</a>
            <a href="macaron_citron.php">fruits exotiques</a>
            <a href="macaron_choco.php">chocolat boisée</a>
        </div>
        <div class="box">
            <h3>contact info</h3>
            <a href="footer.php">+213-0661-381-259</a>
            <a href="footer.php">Noracam.macarons@gmail.com</a>
            <a href="footer.php">Kouba,Alger,Algerie</a>
        </div>
    </div>
    
</section>




<style>
    /*contact us 
    */

    .contacts .rows{
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
    }
    .contacts h1 {
        font-size: 90px;
        margin-top: 40px;
        margin-bottom: 25px;
        margin-left: 1px;
        text-align: center;
    }
    
    .contacts .rows form{
        display: flex;
        flex-direction: column;
        margin-top: 150px;
        height: 550px;
        width: 1500px;
        flex: 1 1 40rem;
        padding: 2rem 1.2rem;
        box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0,.1);
        border:.1rem solid rgba(0, 0, 0,.1);
        background: #f8e7d2;
        border-radius:25px;
        margin-right: 80px;
    }
    .contacts .rows .images{
        flex: 1 1 40rem;
    }
    .contacts .rows .images img{
        width: 65%;
        margin-left: 100px;
    }

    .contacts .rows form .boxs{
        padding: 1rem;
        font-size: 1.7rem;
        color: #333;
        text-transform: none;
        border:.1rem solid rgba(0, 0, 0,.1); ;
        border-radius:.5rem;
        margin:.7rem 0;
        width: 90%;
    }
    .contacts .rows form .boxs:hover{
        border-color: rgb(236, 158, 171);

    }
    .contacts .rows form textarea{
        height: 10rem;
        resize:none;
    }
    .contacts .rows form .btns{
        width: 300px;
        margin-left: 480px;
        margin-top: 40px;
        height: 50px;
        border-radius: 25px;
        font-size: 30px;
        font-weight: 4px;
        background: #000000;
        color: rgb(250, 245, 228);
    }
    .contacts .rows form .btns:hover{
        color:#b4752d;
        border-color:#b4752d;
        background: rgb(250, 245, 228);
    }




    /*suite de contact us*/

    .footer{
        background: #b4752d;
    }
    .footer .box-container{
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-left: 150px;

    }
    .footer .box-container .box{
        flex: 1 1 25rem;
        margin-bottom: 20px;
    }
    .footer .box-container h3{
        color: #f8e7d2;
        font-size: 3.5rem;
        padding: 1rem 0;
        margin-bottom: 2%;

    }
    .footer .box-container .box a{
        display: block;
        color:#161515;
        font-size: 2rem;
        padding:1rem 0;
        
    }
    .footer .box-container .box a:hover{
        color: rgb(224, 165, 88);
        text-decoration: underline;
    }
   
</style>