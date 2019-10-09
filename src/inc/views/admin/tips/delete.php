		<form action="<?php echo $action; ?>" method="POST">
			<input type="hidden" id="action" name="action" value="delete">
            <input type="hidden" name="tip_id" value="<?php echo $tip['id']; ?>">
            <section>
                <header><h3>Delete Tip</h3></header>
                <p>Are you sure you want to delete <b>tip #<?php echo $tip['id']; ?></b> from the database?</p>
                <input type="submit" name="confirm_delete" value="Yes">&nbsp;<input type="submit" name="cancel_delete" value="No">
            </section>
        </form>
