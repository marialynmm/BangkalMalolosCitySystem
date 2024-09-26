@echo off
echo Starting script...
echo Current directory: %CD%

:: Change to the directory where forecast.py is located
cd C:\xampp\htdocs\BangkalMalolosCitySystem\Backend\python

:: Check if forecast.py exists
if exist forecast.py (
    echo forecast.py found.
) else (
    echo forecast.py not found.
    exit /b 1
)

:: Full path to the activation file
set VENV_ACTIVATE=C:\xampp\htdocs\BangkalMalolosCitySystem\.venv\Scripts\activate.bat

:: Check if the virtual environment activation file exists
if exist "%VENV_ACTIVATE%" (
    echo Virtual environment activation file found.
) else (
    echo Virtual environment activation file not found at %VENV_ACTIVATE%.
    exit /b 1
)

call "%VENV_ACTIVATE%"
if errorlevel 1 (
    echo Failed to activate virtual environment. Check the path.
    exit /b 1
)
echo Virtual environment activated.

:: Check if Flask is installed, if not, install it
pip show flask >nul 2>&1
if errorlevel 1 (
    echo Flask not found. Installing...
    pip install flask
)

:: Check if Python is available
where python
if errorlevel 1 (
    echo Python is not recognized. Ensure Python is installed and added to PATH.
    exit /b 1
)

python forecast.py
if errorlevel 1 (
    echo Python script failed to execute. Check if the script exists.
    exit /b 1
)

echo Script completed successfully.
pause
