import Header from "../components/Header"
import Footer from "../components/Footer"
import { useState , useEffect } from "react";
import axios from "axios";

const Services = () => {


    
    const [users, setUsers] = useState([]);

    const fetchuser = () => {
        axios.get('http://localhost:8000/api/user.php')
            .then(response => {
                setUsers(response.data);
            })
            .catch(error => {
                console.error('There was an error fetching the users!', error);
            });
    };

    useEffect(() =>{
        fetchuser();
    },[]);

    return (

        <div className="flex flex-col min-h-screen ">

        <Header />

        <section className="rounded-md bg-white mt-8">
                <div className="py-8 px-4 mx-10 max-w-2xl">
                    <h2 className="mb-4 text-xl font-bold text-gray-900">Bookings</h2>
                    <ul>
                        {users.map(user => (
                            <li key={user.id} className="mb-4 border-b border-gray-200 pb-4">
                                <p><strong>Name:</strong> {user.name}</p>
                                <p><strong>Pickup Location:</strong> {user.pickup}</p>
                                <p><strong>Dropoff Location:</strong> {user.dropoff}</p>
                                <p><strong>Car:</strong> {user.car}</p>
                                <p><strong>Passengers:</strong> {user.passengers}</p>
                                <p><strong>Description:</strong> {user.description}</p>
                            </li>
                        ))}
                    </ul>
                </div>
            </section>

        <Footer />


        </div>


    )

}

export default Services;