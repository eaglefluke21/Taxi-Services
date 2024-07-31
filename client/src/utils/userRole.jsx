import React from "react";
import { Navigate } from "react-router-dom";
import getRoleFromToken from "./getRoleFromToken";

const userRole = (Component, allowedRoles) => {
    return React.forwardRef((props, ref) => {
      const role = getRoleFromToken();
  
      if (!role || !allowedRoles.includes(role)) {
        // Redirect to a "Not Authorized" page or the login page
        return <Navigate to="/noAuth" />;
      }
  
      return <Component {...props} ref={ref} />;
    });
  };

export default userRole;