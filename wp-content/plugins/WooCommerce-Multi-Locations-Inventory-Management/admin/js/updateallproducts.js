jQuery(document).ready((e) => {
    e("#updateAllStock").on("click", () => {
        let a = e("#updateAllStock").data("products");
        a.forEach((a) => {
            e.ajax({
                url: multi_inventory.ajaxurl,
                type: "post",
                data: { product: a, action: "update_inventory_data" },
                beforeSend() {
                    e("#pup_loader").show(), e("#pup_loader").find(".spinner").css("visibility", "visible");
                },
                success(e) {
                    e && alertify.success(e);
                },
                complete(a) {
                    e("#pup_loader").hide();
                },
            });
        });
    });
});
