<h1 class="mb-4">Billiard Clubs</h1>
<a href="/clubs/create" class="btn btn-success mb-3">Create New Club</a>

<table class="table table-striped table-bordered">
<thead>
<tr>
  <th>ID</th>
  <th>Owner</th>
  <th>Name</th>
  <th>Members</th>
  <th>Address</th>
</tr>
</thead>
<tbody>
<?php foreach($clubs as $c): ?>
<tr>
  <td><?= $c['id'] ?></td>
  <td><?= $c['owner_name'] ?></td>
  <td><?= $c['club_name'] ?></td>
  <td><?= $c['club_member_count'] ?></td>
  <td><?= $c['address'] ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
