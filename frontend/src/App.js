import { useEffect, useState } from "react";

const API = "http://localhost/shopping/backend/api/index.php";//backend address

export default function App() {
    //initialize varliables
    const [stores, setStores] = useState([]);
    const [newStore, setNewStore] = useState("");
    const [selectedStore, setSelectedStore] = useState("");
    const [filterStore, setFilterStore] = useState("");

    const [items, setItems] = useState([]);
    const [itemName, setItemName] = useState("");
    const [itemQty, setItemQty] = useState(1);

    const [editItem, setEditItem] = useState(null);
    //fetches the stores from the backend api and sets them. async for await fetch
    const loadStores = async () => {
        const res = await fetch(`${API}?route=stores`);
        setStores(await res.json());
    };
    
    //If there is a store selected it will only get from that store
    const loadItems = async (storeId = filterStore) => {
    let url;

    if (storeId) {
        url = `${API}?route=items&store_id=${storeId}`;
    } else {
        url = `${API}?route=items`;
    }

    const res = await fetch(url);
    setItems(await res.json());
    };

    //on page load, loads all stores as well as loads all items to the shopping list
    useEffect(() => {
        loadStores();
        loadItems();
    }, []);

    useEffect(() => {//whenever filter store changes loads items
        loadItems();
    }, [filterStore]);
    
    const createStore = async () => {
        if (!newStore.trim()) return;//stores cant be made with no input or empty spaces

        await fetch(`${API}?route=stores`, {//sends the request to the backend
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ name: newStore }),
        });

        setNewStore("");//clears input field
        loadStores();//refreshes stores
    };

    const deleteStore = async (id) => {
        await fetch(`${API}?route=stores&id=${id}`, {//deletes the store with the same id
            method: "DELETE",
        });

        setSelectedStore("");//removes that store from selection
        setFilterStore("");
        loadStores();//refresh stores and items list
        loadItems();
    };

    const createItem = async () => {
        if (!itemName.trim()) return;//if item attempted to be created doesnt have a name return

        await fetch(`${API}?route=items&store_id=${selectedStore}`, {//id in url goes to backend to be posted
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              name: itemName,
              quantity: itemQty,
            }),
        });

        setItemName("");//reset form refresh list
        setItemQty(1);
        loadItems();
    };

    const toggleChecked = async (item) => {
        let newChecked;

        if (Number(item.checked) === 1) {//flips checkbox value
            newChecked = 0;
        } else {
            newChecked = 1;
        }

        await fetch(`${API}?route=items&id=${item.id}`, {//uses fetch to put the edited item
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                name: item.name,
                quantity: item.quantity,
                checked: newChecked,
                store_id: item.store_id
            }),
        });

        loadItems();
    };

    const deleteItem = async (id) => {//deletes item with the correct id
        await fetch(`${API}?route=items&id=${id}`, {
            method: "DELETE",
        });

        loadItems();//refreshes item list
    };
    
    const startEdit = (item) => {//item is copied in order to be edited
        setEditItem({
            id: item.id,
            name: item.name,
            quantity: item.quantity,
            store_id: item.store_id,
        });
    };
    
    const cancelEdit = () => {//when editing is canceled current edited item is set to null
        setEditItem(null);
    };
    
    const saveEdit = async () => {//when edits are done the item edits are put and saved over the original
        await fetch(`${API}?route=items&id=${editItem.id}`, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                name: editItem.name,
                quantity: editItem.quantity,
                checked: items.find(i => i.id === editItem.id)?.checked || 0,
                store_id: editItem.store_id,
            }),
        });

        cancelEdit();//edited item is cleared
        loadItems();//item list refreshed
    };


    return (
        <div className="container py-4">

            
            <h3 className="mb-4">Fantastical Computerized Shopping List</h3>
            <div className="row mb-3">
                <div className="col-md-6">
                    <div className="card p-3 h-100">
                        <h5>Create A New Store</h5>
                        <div className="d-flex gap-2">
                            <input className="form-control" placeholder="Store name" value={newStore} onChange={(e) => setNewStore(e.target.value)}/>
                            <button className="btn btn-success" onClick={createStore}>
                            Add
                            </button>
                        </div>
                    </div>
                </div>
                <div className="col-md-6">
                    <div className="card p-3 h-100">
                        <h5>Add Item To Shopping List</h5>

                        <select className="form-select mb-2" value={selectedStore}onChange={(e) => setSelectedStore(e.target.value)}>
                            <option value="">Select a Store</option>
                            {stores.map((s) => (
                                <option key={s.id} value={s.id}>
                                    {s.name}
                                </option>
                            ))}
                        </select>

                        <div className="d-flex gap-2">

                        <input className="form-control" placeholder="Item name" value={itemName} onChange={(e) => setItemName(e.target.value)}/>

                        <input type="number" className="form-control" value={itemQty} onChange={(e) => setItemQty(Number(e.target.value))} style={{ maxWidth: "100px" }}/>

                        <button className="btn btn-success" onClick={createItem}>Add</button>
                        </div>

                        <div className="mt-2 d-flex justify-content-end">
                            {selectedStore && (
                                <button className="btn btn-sm btn-danger" onClick={() => deleteStore(selectedStore)}>
                                  Delete Selected Store
                                </button>
                            )}
                        </div>
                    </div>
                </div>
            </div>
                <div className="card p-3">
                    <h5>Shopping List: </h5>
                    
                    <select
                        className="form-select mb-3"
                        style={{ maxWidth: "250px" }}
                        value = {filterStore}
                        onChange={(e) => {
                            const value = e.target.value;
                            setFilterStore(value);
                            loadItems(value);
                        }}
                    >
                        <option value="">All Stores</option>
                        {stores.map((s) => (
                            <option key={s.id} value={s.id}>
                                {s.name}
                            </option>
                        ))}
                    </select>
                    
                    {items.map((item) => (
                        <div key={item.id} className="border-bottom py-2">
                         
                        {editItem?.id === item.id ? (
                            <div className="d-flex gap-2 align-items-center">

                                <input className="form-control" value={editItem.name} onChange={(e) => setEditItem({ ...editItem, name: e.target.value })}/>

                                <input type = "number" className = "form-control" style = {{ maxWidth: "100px" }} value = {editItem.quantity} onChange = {(e) => setEditItem({ ...editItem, quantity: e.target.value })}/>

                                <select className="form-select" value={editItem.store_id} onChange={(e) => setEditItem({ ...editItem, store_id: e.target.value })} >
                                  <option value="">Select store</option>
                                    {stores.map((s) => (
                                        <option key={s.id} value={s.id}>{s.name}</option>
                                    ))}
                                </select>

                                <button className="btn btn-success btn-sm" onClick={saveEdit}>
                                    Save
                                </button>

                                <button className="btn btn-secondary btn-sm" onClick={cancelEdit}>
                                  Cancel
                                </button>
                            </div>
                        ) : (
                            <div className="d-flex justify-content-between align-items-center">

                                <div>
                                    <input
                                      type="checkbox"
                                      className="form-check-input me-2"
                                      checked={Number(item.checked) === 1}
                                      onChange={() => toggleChecked(item)}
                                    />

                                    <span
                                        style={{
                                            textDecoration:
                                                Number(item.checked) === 1 ? "line-through" : "none",
                                        }}
                                    >
                                        {item.name} ({item.quantity})
                                    </span>

                                    <small className="text-muted ms-2">
                                        Store: {item.store_name} | Date:{" "}
                                        {new Date(item.created_at).toLocaleDateString()}
                                    </small>
                                </div>

                                <div className="d-flex gap-2">
                                    <button className="btn btn-sm btn-outline-secondary" onClick={() => startEdit(item)}>
                                        Edit
                                    </button>

                                    <button className="btn btn-sm btn-outline-danger" onClick={() => deleteItem(item.id)}>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>
                ))}
            </div>

        </div>
  );
}