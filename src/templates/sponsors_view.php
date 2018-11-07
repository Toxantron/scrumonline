<div class="row">
  <h1 class="col-xs-12">Becoming a sponsor</h1>
  <p class="col-xs-12 col-md-8">
    If you want to support the project, you can become an official sponsor. There are three different levels of sponsoring and each is rewarded 
    with your logo and link in different places. The price for the next level always includes the previous levels privileges. Prices only show 
    the current price for each type and may change over time based on demand. To become an official sponsor please contact 
    <a href="mailto:sponsoring@scrumpoker.online">sponsoring@scrumpoker.online</a> with your company and logo URL, as well as the desired 
    sponsoring level. You will recieve an official invoice for your sponsorship, which makes it tax deductible for companies. Within 7 days after 
    payment the page will be updated with your logo. Alternatively if you contribute developer time or server resources, you can also be listed as a sponsor.
  </p>
 
  <div class="col-xs-12 col-md-4"> 
  <table class="table table-striped">
    <thead>
      <tr><th>Level</th><th>Price annually</th><th>Logo placement</th></tr>
    </thead>
    <tbody>
      <tr><td>Basic</td><td><?= Sponsor::$prices[0] ?>€</td><td>Sponsors tab</td></tr>
      <tr><td>Footer</td><td><?= Sponsor::$prices[1] ?>€</td><td>Footer</td></tr>
      <tr><td>Repo</td><td><?= Sponsor::$prices[2] ?>€</td><td>README on GitHub</td></tr>
    </tbody>
  </table>
  </div>
</div>

<div>
  <h1>Other Sponsors:</h1>

  <?= Sponsor::renderOthers() ?>
</div>

<div>
  <h1>Donors (<?= Sponsor::donorCount() ?> total):</h1>

  <?= Sponsor::renderDonors() ?>
</div>



