document.addEventListener("DOMContentLoaded", async () => {
    let ordersList = document.querySelector(".allorders");

    let orders = await getOrders();

    for (const order in orders["orders"]) {
        ordersList.innerHTML += `
        <tr>
                <td>
                    <a href="../DetailOrderPage/index.html?id=${order}">
                        <div class="Order">
                            <span class="info"
                                >Order ID: <span class="Onum">${order}</span></span
                            >

                            <span class="Odate">
                                <span class="info">Date :</span>${orders["orders"][order].date}</span
                            >
                            <span class="info"
                                >Total Price:
                                <span class="Ototal">${orders["orders"][order].price}</span></span
                            >
                        </div>
                    </a>
                </td>
            </tr>
        `;
    }

    async function getOrders() {
        const data = {
            functionName: "getOrders",
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
