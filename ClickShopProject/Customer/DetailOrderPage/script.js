document.addEventListener("DOMContentLoaded", async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get("id"); // Get the value of the "id" parameter

    let orderId = document.querySelector(".OID");
    let orderDate = document.querySelector(".ODate");
    let orderTotal = document.querySelector(".OTotal");
    let orderTable = document.querySelector(".productsOrder");

    let orderDetails = await getOrderDetails();

    for (const product in orderDetails["products"]) {
        orderTable.innerHTML += `
        <tr>
        <div class="order">
            <img
                class="imgCard"
                src="../../Media/ProductImages/${
                    orderDetails["products"][product].image
                }"
                alt="Product Image"
            />
            <span class="info"
                >Name: <span class="OName">${
                    orderDetails["products"][product].name
                }</span></span
            >
            <span class="info"
                >Quantity: <span class="OQuantity">${
                    orderDetails["products"][product].qty
                }</span></span
            >
            <span class="info"
                >Price: <span class="OPrice">${
                    orderDetails["products"][product].qty *
                    orderDetails["products"][product].price
                }</span></span
            >
        </div>
    </tr>
        `;
    }

    orderId.innerHTML = id;
    orderDate.innerHTML = orderDetails["orderDate"];
    orderTotal.innerHTML = orderDetails["orderTotal"];

    async function getOrderDetails() {
        const data = {
            id: id,
            functionName: "getOrderDetails",
        };

        console.log(data);

        try {
            console.log(JSON.stringify(data));

            let response = await fetch("../../PHP/ProductsManagement.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data),
            });
            console.log("hi");
            // console.log(response.json());
            console.log("hi");
            // console.log(response.json());
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        } catch (error) {
            console.error("Error:", error.message);
        }
    }
});
