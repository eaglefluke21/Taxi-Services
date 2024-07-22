import React, { useState, useEffect} from "react";
import axios from 'axios';



const DetailsBooking = () => {

    const [users, setUsers] = useState([]);
    const [formData, setFormData] = useState({
        name: '',
        pickup: '',
        dropoff: '',
        car:'',
        passengers: '',
        description: '',
    });


  

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
    };

   
    const handleSubmit = (e) => {
        e.preventDefault();
        
        axios.post('http://localhost:8000/api/customerbooking.php', formData)
            .then(response => {
                setUsers([...users, response.data]);
                setFormData({ name: '', pickup: '', dropoff: '', car: '', passengers: '', description: '' }); 
                console.log('Data Inserted Successfully');
                fetchuser();
            })
            .catch(error => {
                console.error('There was an error creating the user!', error);
            });
    };


    return (

        <div>

        <section className=" rounded-md bg-amber-300 ">
            <div className="py-8 px-4 mx-10 max-w-2xl ">
                <h2 className="mb-4 text-xl font-bold text-gray-900 font-quick">Book your Cab</h2>
                
                <form onSubmit={handleSubmit}>

                    <div className="grid gap-4 sm:grid-cols-2 sm:gap-6">

                        <div className="sm:col-span-2">

                            <label className="block mb-2 text-sm font-bold text-gray-900 font-quick ">Customer Name</label>

                            <input type="text" name="name" id="name" value={formData.name} onChange={handleChange} className="bg-white border-2 border-black text-gray-900 text-sm rounded-lg  focus:border-gray-600 block w-full p-2.5 " placeholder="name" required />
                        </div>

                        <div className="w-full">
                            <label className="block mb-2 text-sm font-bold text-gray-900 font-quick ">Pick up Location</label>
                            <input type="text" name="pickup" id="pickup" value={formData.pickup} onChange={handleChange} className="bg-white border-2 border-black text-gray-900 text-sm rounded-lg  focus:border-gray-600 block w-full p-2.5 " placeholder="Jaipur" required />
                        </div>

                        <div className="w-full">
                            <label className="block mb-2 text-sm font-bold text-gray-900 font-quick">Drop off Location</label>
                            <input type="text" name="dropoff" id="dropoff" value={formData.dropoff} onChange={handleChange} className="bg-white border-2 border-black text-gray-900 text-sm rounded-lg  focus:border-gray-600 block w-full p-2.5 " placeholder="Delhi" required />
                        </div>
                        <div>

                            <label className="block mb-2 text-sm font-bold text-gray-900 font-quick">Car</label>
                            <select  name="car" value={formData.car} onChange={handleChange} className="bg-white border-2 border-black text-gray-900 text-sm rounded-lg  focus:border-gray-500 block w-full p-2.5 ">
                                <option >Select category</option>
                                <option value="xl6">XL6</option>
                                <option value="ertiga">Ertiga</option>
                                <option value="triber">Triber</option>
                                <option value="ciaz">Ciaz</option>
                            </select>
                        </div>

                        <div>
                            <label className="block mb-2 text-sm font-bold text-gray-900 font-quick">Passengers</label>
                            <input type="number" name="passengers" value={formData.passengers} onChange={handleChange} className="bg-white border-2 border-black text-gray-900 text-sm rounded-lg  focus:border-gray-600 block w-full p-2.5" placeholder="0" required />
                        </div>

                        <div className="sm:col-span-2">
                            <label className="block mb-2 text-sm font-bold text-gray-900 font-quick">Description</label>
                            <textarea  name="description" value={formData.description} onChange={handleChange} className="block p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border-2 border-black  focus:border-gray-500 " placeholder="Any query write here"></textarea>
                        </div>

                    </div>

                    <button type="submit" className="w-full bg-black rounded-md py-1.5 mt-3 font-quick font-semibold shadow-sm shadow-black text-white "> Book </button>

                       
                </form>
            </div>
        </section>


        </div>


    )
}


export default DetailsBooking;