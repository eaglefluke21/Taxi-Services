# Read each line in the .env.local file
Get-Content .env.local | ForEach-Object {
    # Split each line into the variable name and value
    if ($_ -match "^(.*?)=(.*)$") {
        $name = $matches[1].Trim()
        $value = $matches[2].Trim()

        # Set the environment variable
        Set-Item -Path "Env:\$name" -Value $value
    }
}
