import React from "react";

const Popup = ({message,onClose}) => {

    return(
        <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-70">
        <div className="bg-white p-20 rounded-md shadow-md">
            <p className="text-lg font-comic font-semibold my-4">{message}</p>
            <button onClick={onClose} className="px-6 py-2 bg-black text-white rounded-md shadow-md">Close</button>
        </div>
    </div>
    );
};

export default Popup;