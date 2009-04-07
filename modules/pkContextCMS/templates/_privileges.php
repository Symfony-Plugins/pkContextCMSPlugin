<?php if (isset($form[$widget])): ?>
  <div id="pk-context-cms-settings-right">
    <div class="pk-context-cms-form-row">
  
      <label><?php echo $label ?></label>
      <div class="pk-context-cms-local-editors">
        <h4>Local</h4>
        <?php echo $form[$widget] ?>
      </div>

      <?php if (count($inherited) > 0): ?>
      <div class="pk-context-cms-inherited-editors">
        <h4>Inherited</h4>
        <ul>
        <?php foreach($inherited as $editorName): ?>
          <li><?php echo htmlspecialchars($editorName) ?></li>
        <?php endforeach ?>
        </ul>
        <?php if (0): ?>
          <h4>Admin</h4>
          <ul>
          <?php foreach($executive as $editorName): ?>
            <li><?php echo htmlspecialchars($editorName) ?></li>
          <?php endforeach ?>
          </ul>
        <?php endif ?>
      </div>
      <?php endif ?>
    
    </div>
  </div>
<?php endif ?>
