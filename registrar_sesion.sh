#!/bin/bash

# Script para organizar los históricos de conversación de Gemini CLI
# Uso: Ejecuta este script DESPUÉS de haber usado el comando /chat share en la CLI.

FECHA=$(date +"%Y-%m-%d_%H-%M")
NOMBRE_DEFAULT="sesion_gemini_$FECHA.md"
FOLDER="historico_gemini"

echo "--------------------------------------------------------"
echo "  REDA - Organizador de Memoria Gemini"
echo "--------------------------------------------------------"

if [ -f "last_chat_export.md" ]; then
    mv last_chat_export.md "$FOLDER/$NOMBRE_DEFAULT"
    echo "[OK] Sesión guardada como: $FOLDER/$NOMBRE_DEFAULT"
else
    echo "[!] No se encontró 'last_chat_export.md'."
    echo "Por favor, sigue estos pasos:"
    echo "  1. En la consola de Gemini escribe: /chat share last_chat_export.md"
    echo "  2. Vuelve a ejecutar este script."
fi
echo "--------------------------------------------------------"
