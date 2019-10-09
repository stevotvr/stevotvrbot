        <form action="<?php echo $action; ?>" method="POST">
			<input type="hidden" id="action" name="action" value="delete">
            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
            <section>
                <header><h3>Delete <?php echo $item['name']; ?></h3></header>
                <p>Are you sure you want to delete <b><?php echo $item['name']; ?></b> from the items database?</p>
                <input type="submit" name="confirm_delete" value="Yes">&nbsp;<input type="submit" name="cancel_delete" value="No">
            </section>
        </form>
