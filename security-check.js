#!/usr/bin/env node

/**
 * Script de verificación de seguridad de tokens JWT
 * Para Sol Store Ecommerce
 */

const crypto = require('crypto');
const fs = require('fs');
const path = require('path');

class JWTSecurityChecker {
    constructor() {
        this.results = {
            scores: {},
            recommendations: [],
            issues: [],
            status: 'PASS'
        };
    }

    // Analizar la fortaleza del JWT Secret
    analyzeJWTSecret(secret) {
        console.log('🔍 Analizando JWT Secret...');
        
        // Remover prefijo base64: si existe
        const cleanSecret = secret.replace('base64:', '');
        
        const analysis = {
            length: cleanSecret.length,
            hasUppercase: /[A-Z]/.test(cleanSecret),
            hasLowercase: /[a-z]/.test(cleanSecret),
            hasNumbers: /[0-9]/.test(cleanSecret),
            hasSpecialChars: /[^A-Za-z0-9]/.test(cleanSecret),
            entropy: this.calculateEntropy(cleanSecret)
        };

        let score = 0;
        let issues = [];

        // Verificar longitud (mínimo 32 caracteres para HMAC-SHA256)
        if (analysis.length >= 64) {
            score += 30;
            console.log('✅ Longitud del secret: EXCELENTE (64+ caracteres)');
        } else if (analysis.length >= 32) {
            score += 20;
            console.log('✅ Longitud del secret: BUENA (32+ caracteres)');
        } else {
            score += 0;
            issues.push('Secret demasiado corto (< 32 caracteres)');
            console.log('❌ Longitud del secret: INSUFICIENTE (< 32 caracteres)');
        }

        // Verificar complejidad
        if (analysis.hasUppercase) score += 10;
        if (analysis.hasLowercase) score += 10;
        if (analysis.hasNumbers) score += 10;
        if (analysis.hasSpecialChars) score += 10;

        // Verificar entropía
        if (analysis.entropy > 4.5) {
            score += 30;
            console.log('✅ Entropía del secret: ALTA');
        } else if (analysis.entropy > 3.5) {
            score += 20;
            console.log('⚠️ Entropía del secret: MEDIA');
        } else {
            score += 0;
            issues.push('Entropía baja del secret');
            console.log('❌ Entropía del secret: BAJA');
        }

        this.results.scores.jwtSecret = score;
        this.results.issues.push(...issues);

        return analysis;
    }

    // Calcular entropía de Shannon
    calculateEntropy(str) {
        const freq = {};
        for (let char of str) {
            freq[char] = (freq[char] || 0) + 1;
        }

        let entropy = 0;
        const len = str.length;
        
        for (let char in freq) {
            const p = freq[char] / len;
            entropy -= p * Math.log2(p);
        }

        return entropy;
    }

    // Verificar configuración JWT
    analyzeJWTConfig(config) {
        console.log('\n🔧 Analizando configuración JWT...');
        
        let score = 0;
        let issues = [];

        // TTL (Time To Live)
        const ttl = parseInt(config.JWT_TTL || 60);
        if (ttl <= 60) {
            score += 30;
            console.log('✅ JWT TTL: SEGURO (≤ 60 minutos)');
        } else if (ttl <= 120) {
            score += 20;
            console.log('⚠️ JWT TTL: ACEPTABLE (≤ 120 minutos)');
        } else {
            score += 0;
            issues.push('TTL demasiado largo (> 120 minutos)');
            console.log('❌ JWT TTL: INSEGURO (> 120 minutos)');
        }

        // Refresh TTL
        const refreshTtl = parseInt(config.JWT_REFRESH_TTL || 20160);
        if (refreshTtl <= 20160) { // 14 días
            score += 20;
            console.log('✅ Refresh TTL: APROPIADO (≤ 14 días)');
        } else {
            score += 10;
            console.log('⚠️ Refresh TTL: Considerar reducir');
        }

        // Algoritmo
        const algo = config.JWT_ALGO || 'HS256';
        if (['HS256', 'HS384', 'HS512'].includes(algo)) {
            score += 20;
            console.log('✅ Algoritmo JWT: SEGURO (' + algo + ')');
        } else if (['RS256', 'RS384', 'RS512'].includes(algo)) {
            score += 30;
            console.log('✅ Algoritmo JWT: MUY SEGURO (' + algo + ')');
        } else {
            score += 0;
            issues.push('Algoritmo JWT inseguro o desconocido');
            console.log('❌ Algoritmo JWT: INSEGURO (' + algo + ')');
        }

        // Blacklist habilitada
        if (config.JWT_BLACKLIST_ENABLED !== 'false') {
            score += 30;
            console.log('✅ JWT Blacklist: HABILITADA');
        } else {
            issues.push('JWT Blacklist deshabilitada');
            console.log('❌ JWT Blacklist: DESHABILITADA');
        }

        this.results.scores.jwtConfig = score;
        this.results.issues.push(...issues);

        return { ttl, refreshTtl, algo, score };
    }

    // Verificar configuración de sesión
    analyzeSessionConfig(config) {
        console.log('\n🍪 Analizando configuración de sesión...');
        
        let score = 0;
        let issues = [];

        // HTTP Only
        if (config.SESSION_HTTP_ONLY !== 'false') {
            score += 25;
            console.log('✅ Session HTTP Only: HABILITADO');
        } else {
            issues.push('SESSION_HTTP_ONLY deshabilitado');
            console.log('❌ Session HTTP Only: DESHABILITADO');
        }

        // Same Site
        const sameSite = config.SESSION_SAME_SITE || 'lax';
        if (sameSite === 'strict') {
            score += 30;
            console.log('✅ Session SameSite: STRICT (máxima seguridad)');
        } else if (sameSite === 'lax') {
            score += 25;
            console.log('✅ Session SameSite: LAX (buena seguridad)');
        } else {
            score += 0;
            issues.push('SESSION_SAME_SITE inseguro');
            console.log('❌ Session SameSite: INSEGURO (' + sameSite + ')');
        }

        // Encriptación de sesión
        if (config.SESSION_ENCRYPT !== 'false') {
            score += 25;
            console.log('✅ Session Encrypt: HABILITADO');
        } else {
            issues.push('SESSION_ENCRYPT deshabilitado');
            console.log('⚠️ Session Encrypt: DESHABILITADO');
        }

        // Tiempo de vida
        const lifetime = parseInt(config.SESSION_LIFETIME || 120);
        if (lifetime <= 120) {
            score += 20;
            console.log('✅ Session Lifetime: SEGURO (≤ 120 minutos)');
        } else {
            score += 10;
            console.log('⚠️ Session Lifetime: Considerar reducir');
        }

        this.results.scores.sessionConfig = score;
        this.results.issues.push(...issues);

        return { score, lifetime, sameSite };
    }

    // Verificar configuración CORS
    analyzeCORSConfig(corsConfig) {
        console.log('\n🌐 Analizando configuración CORS...');
        
        let score = 0;
        let issues = [];

        // Verificar orígenes permitidos
        if (corsConfig.includes("'*'") || corsConfig.includes('*')) {
            score += 0;
            issues.push('CORS permite todos los orígenes (*)');
            console.log('❌ CORS Origins: INSEGURO (permite todos)');
        } else if (corsConfig.includes('localhost')) {
            score += 70;
            console.log('✅ CORS Origins: APROPIADO PARA DESARROLLO');
        } else {
            score += 100;
            console.log('✅ CORS Origins: CONFIGURACIÓN ESPECÍFICA');
        }

        this.results.scores.corsConfig = score;
        this.results.issues.push(...issues);

        return { score };
    }

    // Leer archivo .env
    readEnvFile(filePath) {
        try {
            const content = fs.readFileSync(filePath, 'utf8');
            const config = {};
            
            content.split('\n').forEach(line => {
                line = line.trim();
                if (line && !line.startsWith('#')) {
                    const [key, ...valueParts] = line.split('=');
                    if (key && valueParts.length > 0) {
                        config[key.trim()] = valueParts.join('=').trim().replace(/^["']|["']$/g, '');
                    }
                }
            });
            
            return config;
        } catch (error) {
            console.error('Error leyendo archivo .env:', error.message);
            return {};
        }
    }

    // Generar recomendaciones
    generateRecommendations() {
        console.log('\n📋 Generando recomendaciones...');
        
        const recommendations = [];
        
        if (this.results.scores.jwtSecret < 80) {
            recommendations.push('🔑 Generar un nuevo JWT secret más fuerte: php artisan jwt:secret');
        }
        
        if (this.results.scores.jwtConfig < 90) {
            recommendations.push('⏰ Verificar configuración de TTL para tokens JWT');
        }
        
        if (this.results.scores.sessionConfig < 90) {
            recommendations.push('🍪 Habilitar todas las medidas de seguridad para sesiones');
        }
        
        if (this.results.scores.corsConfig < 50) {
            recommendations.push('🌐 Restringir CORS a dominios específicos en producción');
        }

        recommendations.push('🔒 Implementar rate limiting para endpoints de autenticación');
        recommendations.push('📝 Configurar logs de seguridad para intentos de autenticación fallidos');
        recommendations.push('🔄 Implementar rotación periódica de JWT secrets');
        
        this.results.recommendations = recommendations;
    }

    // Calcular puntuación total
    calculateOverallScore() {
        const scores = Object.values(this.results.scores);
        const totalScore = scores.reduce((sum, score) => sum + score, 0);
        const maxScore = scores.length * 100;
        const percentage = Math.round((totalScore / maxScore) * 100);
        
        console.log('\n📊 PUNTUACIÓN TOTAL DE SEGURIDAD');
        console.log('=' .repeat(50));
        
        Object.entries(this.results.scores).forEach(([key, score]) => {
            console.log(`${key}: ${score}/100`);
        });
        
        console.log('-'.repeat(50));
        console.log(`TOTAL: ${totalScore}/${maxScore} (${percentage}%)`);
        
        if (percentage >= 90) {
            console.log('🟢 ESTADO: EXCELENTE SEGURIDAD');
            this.results.status = 'EXCELLENT';
        } else if (percentage >= 75) {
            console.log('🟡 ESTADO: BUENA SEGURIDAD');
            this.results.status = 'GOOD';
        } else if (percentage >= 60) {
            console.log('🟠 ESTADO: SEGURIDAD ACEPTABLE');
            this.results.status = 'ACCEPTABLE';
        } else {
            console.log('🔴 ESTADO: SEGURIDAD INSUFICIENTE');
            this.results.status = 'INSUFFICIENT';
        }
        
        return percentage;
    }

    // Ejecutar análisis completo
    runFullAnalysis() {
        console.log('🔐 ANÁLISIS DE SEGURIDAD JWT - SOL STORE ECOMMERCE');
        console.log('=' .repeat(60));
        
        const envPath = path.join(__dirname, 'taskcurso', '.env');
        const config = this.readEnvFile(envPath);
        
        if (!config.JWT_SECRET) {
            console.error('❌ No se encontró JWT_SECRET en el archivo .env');
            return;
        }

        // Análisis del JWT Secret
        this.analyzeJWTSecret(config.JWT_SECRET);
        
        // Análisis de configuración JWT
        this.analyzeJWTConfig(config);
        
        // Análisis de configuración de sesión
        this.analyzeSessionConfig(config);
        
        // Análisis de CORS (simulado)
        this.analyzeCORSConfig(['http://localhost:5173']);
        
        // Generar recomendaciones
        this.generateRecommendations();
        
        // Calcular puntuación total
        const overallScore = this.calculateOverallScore();
        
        // Mostrar problemas encontrados
        if (this.results.issues.length > 0) {
            console.log('\n⚠️ PROBLEMAS ENCONTRADOS:');
            this.results.issues.forEach((issue, index) => {
                console.log(`${index + 1}. ${issue}`);
            });
        }
        
        // Mostrar recomendaciones
        console.log('\n💡 RECOMENDACIONES:');
        this.results.recommendations.forEach((rec, index) => {
            console.log(`${index + 1}. ${rec}`);
        });
        
        console.log('\n✅ Análisis completado exitosamente!');
        
        return this.results;
    }
}

// Ejecutar el análisis
if (require.main === module) {
    const checker = new JWTSecurityChecker();
    checker.runFullAnalysis();
}

module.exports = JWTSecurityChecker;